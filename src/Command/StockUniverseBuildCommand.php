<?php

namespace App\Command;

use App\Service\StockImport\StockUniverseBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:stocks:universe:build', description: 'Build a stock universe list')]
final class StockUniverseBuildCommand extends Command
{
    public function __construct(
        private readonly StockUniverseBuilder $builder,
        private readonly string $defaultOutput,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('count', null, InputOption::VALUE_REQUIRED, 'Target number of symbols', 1000)
            ->addOption('etfs', null, InputOption::VALUE_REQUIRED, 'Number of ETFs to include', 50)
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Output JSON file', $this->defaultOutput)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not write file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = (int) $input->getOption('count');
        $etfs = (int) $input->getOption('etfs');
        $outputFile = (string) $input->getOption('output');

        $io->writeln(sprintf('Building universe: target=%d, etfs=%d', $count, $etfs));
        $symbols = $this->builder->build($count, $etfs);

        $io->writeln(sprintf('Built %d symbols.', \count($symbols)));

        if ($input->getOption('dry-run')) {
            return Command::SUCCESS;
        }

        if ($outputFile === '') {
            $io->error('Output file not configured.');
            return Command::FAILURE;
        }

        $json = json_encode($symbols, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $io->error('Failed to encode symbols.');
            return Command::FAILURE;
        }

        $dir = \dirname($outputFile);
        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            $io->error(sprintf('Unable to create output directory: %s', $dir));
            return Command::FAILURE;
        }

        file_put_contents($outputFile, $json);
        $io->success(sprintf('Universe written to %s', $outputFile));

        return Command::SUCCESS;
    }
}
