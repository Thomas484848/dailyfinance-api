<?php

namespace App\Command;

use App\Aggregation\Orchestrator;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:refresh-aggregator',
    description: 'Refresh instruments from providers with rate limiting and cache',
)]
class RefreshAggregatorCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Orchestrator $orchestrator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('batch', null, InputOption::VALUE_OPTIONAL, 'Batch size', 50)
            ->addOption('stale', null, InputOption::VALUE_OPTIONAL, 'Stale seconds', 3600)
            ->addOption('loop', null, InputOption::VALUE_NONE, 'Run continuously');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $batchSize = max(1, (int) $input->getOption('batch'));
        $staleSeconds = max(1, (int) $input->getOption('stale'));
        $loop = (bool) $input->getOption('loop');

        do {
            $batch = $this->fetchStaleBatch($batchSize);
            $now = time();
            $stale = array_filter($batch, function (array $item) use ($now, $staleSeconds): bool {
                if ($item['as_of_date'] === null) {
                    return true;
                }
                return ($now - $item['as_of_date']) > $staleSeconds;
            });

            if ($stale === []) {
                $io->writeln('[refresh] Nothing stale');
                if ($loop) {
                    sleep(60);
                }
                continue;
            }

            $io->writeln(sprintf('[refresh] Batch size %d', count($stale)));
            $this->runBatch($io, array_column($stale, 'id'));
            if ($loop) {
                sleep(1);
            }
        } while ($loop);

        return Command::SUCCESS;
    }

    private function fetchStaleBatch(int $limit): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT i.id as id, MAX(m.as_of_date) as as_of_date
             FROM instrument i
             LEFT JOIN metrics m ON m.instrument_id = i.id
             WHERE i.active = true
             GROUP BY i.id
             ORDER BY CASE WHEN MAX(m.as_of_date) IS NULL THEN 0 ELSE 1 END, MAX(m.as_of_date) ASC
             LIMIT :limit',
            ['limit' => $limit]
        );

        return array_map(function (array $row): array {
            $asOf = $row['as_of_date'] ?? null;
            return [
                'id' => $row['id'],
                'as_of_date' => $asOf ? strtotime($asOf) : null,
            ];
        }, $rows);
    }

    private function runBatch(SymfonyStyle $io, array $instrumentIds): void
    {
        $total = count($instrumentIds);
        $count = 0;
        foreach ($instrumentIds as $id) {
            try {
                $this->orchestrator->enrichInstrument($id, true);
            } catch (\Throwable $error) {
                $io->writeln(sprintf('[refresh] Error: %s', $error->getMessage()));
            }
            $count++;
            if ($count % 10 === 0 || $count === $total) {
                $io->writeln(sprintf('[refresh] Progress %d/%d', $count, $total));
            }
        }
    }
}
