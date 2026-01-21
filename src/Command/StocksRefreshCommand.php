<?php

namespace App\Command;

use App\Service\StockImport\StockImportService;
use App\Service\StockImport\StockUniverse;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:stocks:refresh', description: 'Refresh stock data from external providers')]
final class StocksRefreshCommand extends Command
{
    public function __construct(
        private readonly StockImportService $importService,
        private readonly StockUniverse $universe,
        private readonly int $loopSleepMs,
        private readonly int $idleSleepMs,
        private readonly \App\Service\StockImport\StockRefreshLogger $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('once', null, InputOption::VALUE_NONE, 'Run a single refresh cycle')
            ->addOption('symbols', null, InputOption::VALUE_REQUIRED, 'Comma-separated symbols override');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $lockHandle = $this->acquireLock($io);
        if ($lockHandle === null) {
            return Command::SUCCESS;
        }

        $symbols = $this->resolveSymbols($input);
        $runOnce = (bool) $input->getOption('once');

        if ($symbols === []) {
            $io->warning('No symbols configured.');
            $this->logger->warning('No symbols configured.');
            return Command::SUCCESS;
        }

        do {
            $io->writeln(sprintf('Refreshing %d symbols...', count($symbols)));
            $this->logger->info('Refresh cycle start', ['count' => count($symbols)]);
            $updated = 0;

            foreach ($symbols as $symbol) {
                $startedAt = microtime(true);
                $io->writeln(sprintf('[%s] start', $symbol));
                $stock = $this->importService->importSymbol($symbol, function (string $message, array $context = []) use ($io, $symbol): void {
                    $suffix = $context === [] ? '' : ' | '.json_encode($context, JSON_UNESCAPED_SLASHES);
                    $io->writeln(sprintf('[%s] %s%s', $symbol, $message, $suffix));
                });
                if ($stock) {
                    $updated++;
                    $duration = number_format((microtime(true) - $startedAt), 2);
                    $io->writeln(sprintf('[%s] done in %ss', $symbol, $duration));
                } else {
                    $duration = number_format((microtime(true) - $startedAt), 2);
                    $io->writeln(sprintf('[%s] skipped in %ss', $symbol, $duration));
                }
            }

            $io->writeln(sprintf('Cycle done. Updated %d symbols.', $updated));
            $this->logger->info('Refresh cycle end', ['updated' => $updated]);

            if ($runOnce) {
                break;
            }

            $sleepMs = $updated > 0 ? $this->loopSleepMs : $this->idleSleepMs;
            if ($sleepMs > 0) {
                usleep($sleepMs * 1000);
                $this->logger->info('Sleeping between cycles', ['sleepMs' => $sleepMs]);
            }
        } while (true);

        $this->releaseLock($lockHandle);
        return Command::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function resolveSymbols(InputInterface $input): array
    {
        $override = (string) $input->getOption('symbols');
        if ($override !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $override))));
        }

        return $this->universe->getSymbols();
    }

    /**
     * @return resource|null
     */
    private function acquireLock(SymfonyStyle $io)
    {
        $lockFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'dailyfinance_stocks_refresh.lock';
        $handle = @fopen($lockFile, 'c+');
        if ($handle === false) {
            $io->warning('Unable to create lock file. Proceeding without lock.');
            return null;
        }

        if (!flock($handle, \LOCK_EX | \LOCK_NB)) {
            $io->warning('Another refresh process is already running.');
            fclose($handle);
            return null;
        }

        return $handle;
    }

    /**
     * @param resource $handle
     */
    private function releaseLock($handle): void
    {
        flock($handle, \LOCK_UN);
        fclose($handle);
    }
}
