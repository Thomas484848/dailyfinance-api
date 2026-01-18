<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use PDO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-sqlite',
    description: 'Import aggregator data from a SQLite file into Postgres',
)]
class ImportSqliteCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to prisma dev.db (SQLite)')
            ->addOption('batch', null, InputOption::VALUE_OPTIONAL, 'Batch size', 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getOption('path') ?? getenv('SQLITE_IMPORT_PATH');
        $batchSize = (int) ($input->getOption('batch') ?? 500);

        if (!$path) {
            $io->error('Missing --path or SQLITE_IMPORT_PATH');
            return Command::FAILURE;
        }
        if (!is_file($path)) {
            $io->error(sprintf('SQLite file not found: %s', $path));
            return Command::FAILURE;
        }

        $io->section('Connecting to SQLite');
        $sqlite = new PDO('sqlite:' . $path);
        $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $io->section('Importing tables');
        $this->importInstruments($io, $sqlite, $batchSize);
        $this->importSymbolAliases($io, $sqlite, $batchSize);
        $this->importProviderCache($io, $sqlite, $batchSize);
        $this->importMetrics($io, $sqlite, $batchSize);
        $this->importJobs($io, $sqlite, $batchSize);
        $this->importJobRuns($io, $sqlite, $batchSize);

        $io->success('Import completed');
        return Command::SUCCESS;
    }

    private function importInstruments(SymfonyStyle $io, PDO $sqlite, int $batchSize): void
    {
        $io->text('instrument');
        $stmt = $sqlite->query('SELECT * FROM Instrument');
        $sql = 'INSERT INTO instrument (id, symbol, exchange_code, name, country, currency, sector, industry, isin, active, created_at, updated_at)
                VALUES (:id, :symbol, :exchange_code, :name, :country, :currency, :sector, :industry, :isin, :active, :created_at, :updated_at)
                ON CONFLICT (id) DO NOTHING';

        $this->bulkInsert($stmt, $sql, $batchSize, function (array $row): array {
            return [
                'id' => $row['id'],
                'symbol' => $row['symbol'],
                'exchange_code' => $row['exchangeCode'] ?? null,
                'name' => $row['name'] ?? null,
                'country' => $row['country'] ?? null,
                'currency' => $row['currency'] ?? null,
                'sector' => $row['sector'] ?? null,
                'industry' => $row['industry'] ?? null,
                'isin' => $row['isin'] ?? null,
                'active' => $this->normalizeBoolean($row['active'] ?? true),
                'created_at' => $this->normalizeDateTime($row['createdAt'] ?? null),
                'updated_at' => $this->normalizeDateTime($row['updatedAt'] ?? null),
            ];
        });
    }

    private function importSymbolAliases(SymfonyStyle $io, PDO $sqlite, int $batchSize): void
    {
        $io->text('symbol_alias');
        $stmt = $sqlite->query('SELECT * FROM SymbolAlias');
        $sql = 'INSERT INTO symbol_alias (id, instrument_id, provider, symbol, exchange_code, created_at)
                VALUES (:id, :instrument_id, :provider, :symbol, :exchange_code, :created_at)
                ON CONFLICT (id) DO NOTHING';

        $this->bulkInsert($stmt, $sql, $batchSize, function (array $row): array {
            return [
                'id' => $row['id'],
                'instrument_id' => $row['instrumentId'],
                'provider' => $row['provider'],
                'symbol' => $row['symbol'],
                'exchange_code' => $row['exchangeCode'] ?? null,
                'created_at' => $this->normalizeDateTime($row['createdAt'] ?? null),
            ];
        });
    }

    private function importProviderCache(SymfonyStyle $io, PDO $sqlite, int $batchSize): void
    {
        $io->text('provider_cache');
        $stmt = $sqlite->query('SELECT * FROM ProviderCache');
        $sql = 'INSERT INTO provider_cache (id, instrument_id, provider, endpoint, payload_json, fetched_at, ttl_seconds)
                VALUES (:id, :instrument_id, :provider, :endpoint, :payload_json, :fetched_at, :ttl_seconds)
                ON CONFLICT (id) DO NOTHING';

        $this->bulkInsert($stmt, $sql, $batchSize, function (array $row): array {
            return [
                'id' => $row['id'],
                'instrument_id' => $row['instrumentId'],
                'provider' => $row['provider'],
                'endpoint' => $row['endpoint'],
                'payload_json' => $row['payloadJson'],
                'fetched_at' => $this->normalizeDateTime($row['fetchedAt'] ?? null),
                'ttl_seconds' => (int) ($row['ttlSeconds'] ?? 0),
            ];
        });
    }

    private function importMetrics(SymfonyStyle $io, PDO $sqlite, int $batchSize): void
    {
        $io->text('metrics');
        $stmt = $sqlite->query('SELECT * FROM Metrics');
        $sql = 'INSERT INTO metrics (
                    id, instrument_id, as_of_date, price, open, high, low, close, volume, market_cap, pe, eps, dividend_yield,
                    revenue_ttm, net_income_ttm, gross_margin, operating_margin, profit_margin, dividend_per_share, payout_ratio,
                    revenue_per_share, eps_diluted, shares_outstanding, float_shares, beta, week52_high, week52_low, avg_volume,
                    enterprise_value, ebitda_ttm, free_cash_flow_ttm, operating_cash_flow_ttm, gross_profit_ttm, total_debt, total_cash,
                    debt_to_equity, current_ratio, quick_ratio, price_to_book, price_to_sales, peg_ratio, ev_to_ebitda, ev_to_revenue,
                    book_value_per_share, roa, roe, roi,
                    price_source, open_source, high_source, low_source, close_source, volume_source, market_cap_source, pe_source, eps_source,
                    dividend_yield_source, revenue_ttm_source, net_income_ttm_source, gross_margin_source, operating_margin_source,
                    profit_margin_source, dividend_per_share_source, payout_ratio_source, revenue_per_share_source, eps_diluted_source,
                    shares_outstanding_source, float_shares_source, beta_source, week52_high_source, week52_low_source, avg_volume_source,
                    enterprise_value_source, ebitda_ttm_source, free_cash_flow_ttm_source, operating_cash_flow_ttm_source,
                    gross_profit_ttm_source, total_debt_source, total_cash_source, debt_to_equity_source, current_ratio_source,
                    quick_ratio_source, price_to_book_source, price_to_sales_source, peg_ratio_source, ev_to_ebitda_source,
                    ev_to_revenue_source, book_value_per_share_source, roa_source, roe_source, roi_source
                )
                VALUES (
                    :id, :instrument_id, :as_of_date, :price, :open, :high, :low, :close, :volume, :market_cap, :pe, :eps, :dividend_yield,
                    :revenue_ttm, :net_income_ttm, :gross_margin, :operating_margin, :profit_margin, :dividend_per_share, :payout_ratio,
                    :revenue_per_share, :eps_diluted, :shares_outstanding, :float_shares, :beta, :week52_high, :week52_low, :avg_volume,
                    :enterprise_value, :ebitda_ttm, :free_cash_flow_ttm, :operating_cash_flow_ttm, :gross_profit_ttm, :total_debt, :total_cash,
                    :debt_to_equity, :current_ratio, :quick_ratio, :price_to_book, :price_to_sales, :peg_ratio, :ev_to_ebitda, :ev_to_revenue,
                    :book_value_per_share, :roa, :roe, :roi,
                    :price_source, :open_source, :high_source, :low_source, :close_source, :volume_source, :market_cap_source, :pe_source, :eps_source,
                    :dividend_yield_source, :revenue_ttm_source, :net_income_ttm_source, :gross_margin_source, :operating_margin_source,
                    :profit_margin_source, :dividend_per_share_source, :payout_ratio_source, :revenue_per_share_source, :eps_diluted_source,
                    :shares_outstanding_source, :float_shares_source, :beta_source, :week52_high_source, :week52_low_source, :avg_volume_source,
                    :enterprise_value_source, :ebitda_ttm_source, :free_cash_flow_ttm_source, :operating_cash_flow_ttm_source,
                    :gross_profit_ttm_source, :total_debt_source, :total_cash_source, :debt_to_equity_source, :current_ratio_source,
                    :quick_ratio_source, :price_to_book_source, :price_to_sales_source, :peg_ratio_source, :ev_to_ebitda_source,
                    :ev_to_revenue_source, :book_value_per_share_source, :roa_source, :roe_source, :roi_source
                )
                ON CONFLICT (id) DO NOTHING';

        $this->bulkInsert($stmt, $sql, $batchSize, function (array $row): array {
            return [
                'id' => $row['id'],
                'instrument_id' => $row['instrumentId'],
                'as_of_date' => $this->normalizeDateTime($row['asOfDate'] ?? null),
                'price' => $row['price'] ?? null,
                'open' => $row['open'] ?? null,
                'high' => $row['high'] ?? null,
                'low' => $row['low'] ?? null,
                'close' => $row['close'] ?? null,
                'volume' => $row['volume'] ?? null,
                'market_cap' => $row['marketCap'] ?? null,
                'pe' => $row['pe'] ?? null,
                'eps' => $row['eps'] ?? null,
                'dividend_yield' => $row['dividendYield'] ?? null,
                'revenue_ttm' => $row['revenueTtm'] ?? null,
                'net_income_ttm' => $row['netIncomeTtm'] ?? null,
                'gross_margin' => $row['grossMargin'] ?? null,
                'operating_margin' => $row['operatingMargin'] ?? null,
                'profit_margin' => $row['profitMargin'] ?? null,
                'dividend_per_share' => $row['dividendPerShare'] ?? null,
                'payout_ratio' => $row['payoutRatio'] ?? null,
                'revenue_per_share' => $row['revenuePerShare'] ?? null,
                'eps_diluted' => $row['epsDiluted'] ?? null,
                'shares_outstanding' => $row['sharesOutstanding'] ?? null,
                'float_shares' => $row['floatShares'] ?? null,
                'beta' => $row['beta'] ?? null,
                'week52_high' => $row['week52High'] ?? null,
                'week52_low' => $row['week52Low'] ?? null,
                'avg_volume' => $row['avgVolume'] ?? null,
                'enterprise_value' => $row['enterpriseValue'] ?? null,
                'ebitda_ttm' => $row['ebitdaTtm'] ?? null,
                'free_cash_flow_ttm' => $row['freeCashFlowTtm'] ?? null,
                'operating_cash_flow_ttm' => $row['operatingCashFlowTtm'] ?? null,
                'gross_profit_ttm' => $row['grossProfitTtm'] ?? null,
                'total_debt' => $row['totalDebt'] ?? null,
                'total_cash' => $row['totalCash'] ?? null,
                'debt_to_equity' => $row['debtToEquity'] ?? null,
                'current_ratio' => $row['currentRatio'] ?? null,
                'quick_ratio' => $row['quickRatio'] ?? null,
                'price_to_book' => $row['priceToBook'] ?? null,
                'price_to_sales' => $row['priceToSales'] ?? null,
                'peg_ratio' => $row['pegRatio'] ?? null,
                'ev_to_ebitda' => $row['evToEbitda'] ?? null,
                'ev_to_revenue' => $row['evToRevenue'] ?? null,
                'book_value_per_share' => $row['bookValuePerShare'] ?? null,
                'roa' => $row['roa'] ?? null,
                'roe' => $row['roe'] ?? null,
                'roi' => $row['roi'] ?? null,
                'price_source' => $row['priceSource'] ?? null,
                'open_source' => $row['openSource'] ?? null,
                'high_source' => $row['highSource'] ?? null,
                'low_source' => $row['lowSource'] ?? null,
                'close_source' => $row['closeSource'] ?? null,
                'volume_source' => $row['volumeSource'] ?? null,
                'market_cap_source' => $row['marketCapSource'] ?? null,
                'pe_source' => $row['peSource'] ?? null,
                'eps_source' => $row['epsSource'] ?? null,
                'dividend_yield_source' => $row['dividendYieldSource'] ?? null,
                'revenue_ttm_source' => $row['revenueTtmSource'] ?? null,
                'net_income_ttm_source' => $row['netIncomeTtmSource'] ?? null,
                'gross_margin_source' => $row['grossMarginSource'] ?? null,
                'operating_margin_source' => $row['operatingMarginSource'] ?? null,
                'profit_margin_source' => $row['profitMarginSource'] ?? null,
                'dividend_per_share_source' => $row['dividendPerShareSource'] ?? null,
                'payout_ratio_source' => $row['payoutRatioSource'] ?? null,
                'revenue_per_share_source' => $row['revenuePerShareSource'] ?? null,
                'eps_diluted_source' => $row['epsDilutedSource'] ?? null,
                'shares_outstanding_source' => $row['sharesOutstandingSource'] ?? null,
                'float_shares_source' => $row['floatSharesSource'] ?? null,
                'beta_source' => $row['betaSource'] ?? null,
                'week52_high_source' => $row['week52HighSource'] ?? null,
                'week52_low_source' => $row['week52LowSource'] ?? null,
                'avg_volume_source' => $row['avgVolumeSource'] ?? null,
                'enterprise_value_source' => $row['enterpriseValueSource'] ?? null,
                'ebitda_ttm_source' => $row['ebitdaTtmSource'] ?? null,
                'free_cash_flow_ttm_source' => $row['freeCashFlowTtmSource'] ?? null,
                'operating_cash_flow_ttm_source' => $row['operatingCashFlowTtmSource'] ?? null,
                'gross_profit_ttm_source' => $row['grossProfitTtmSource'] ?? null,
                'total_debt_source' => $row['totalDebtSource'] ?? null,
                'total_cash_source' => $row['totalCashSource'] ?? null,
                'debt_to_equity_source' => $row['debtToEquitySource'] ?? null,
                'current_ratio_source' => $row['currentRatioSource'] ?? null,
                'quick_ratio_source' => $row['quickRatioSource'] ?? null,
                'price_to_book_source' => $row['priceToBookSource'] ?? null,
                'price_to_sales_source' => $row['priceToSalesSource'] ?? null,
                'peg_ratio_source' => $row['pegRatioSource'] ?? null,
                'ev_to_ebitda_source' => $row['evToEbitdaSource'] ?? null,
                'ev_to_revenue_source' => $row['evToRevenueSource'] ?? null,
                'book_value_per_share_source' => $row['bookValuePerShareSource'] ?? null,
                'roa_source' => $row['roaSource'] ?? null,
                'roe_source' => $row['roeSource'] ?? null,
                'roi_source' => $row['roiSource'] ?? null,
            ];
        });
    }

    private function importJobs(SymfonyStyle $io, PDO $sqlite, int $batchSize): void
    {
        $io->text('job');
        $stmt = $sqlite->query('SELECT * FROM Job');
        $sql = 'INSERT INTO job (id, type, status, meta_json, started_at, finished_at, created_at)
                VALUES (:id, :type, :status, :meta_json, :started_at, :finished_at, :created_at)
                ON CONFLICT (id) DO NOTHING';

        $this->bulkInsert($stmt, $sql, $batchSize, function (array $row): array {
            return [
                'id' => $row['id'],
                'type' => $row['type'],
                'status' => $row['status'] ?? 'pending',
                'meta_json' => $row['metaJson'] ?? null,
                'started_at' => $this->normalizeDateTime($row['startedAt'] ?? null),
                'finished_at' => $this->normalizeDateTime($row['finishedAt'] ?? null),
                'created_at' => $this->normalizeDateTime($row['createdAt'] ?? null),
            ];
        });
    }

    private function importJobRuns(SymfonyStyle $io, PDO $sqlite, int $batchSize): void
    {
        $io->text('job_run');
        $stmt = $sqlite->query('SELECT * FROM JobRun');
        $sql = 'INSERT INTO job_run (id, job_id, instrument_id, status, error, started_at, finished_at)
                VALUES (:id, :job_id, :instrument_id, :status, :error, :started_at, :finished_at)
                ON CONFLICT (id) DO NOTHING';

        $this->bulkInsert($stmt, $sql, $batchSize, function (array $row): array {
            return [
                'id' => $row['id'],
                'job_id' => $row['jobId'],
                'instrument_id' => $row['instrumentId'],
                'status' => $row['status'] ?? 'pending',
                'error' => $row['error'] ?? null,
                'started_at' => $this->normalizeDateTime($row['startedAt'] ?? null),
                'finished_at' => $this->normalizeDateTime($row['finishedAt'] ?? null),
            ];
        });
    }

    private function normalizeDateTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            $numeric = (float) $value;
            if ($numeric > 100000000000) {
                $numeric = $numeric / 1000;
            }
            return gmdate('Y-m-d H:i:s', (int) $numeric);
        }
        if (is_string($value)) {
            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                return gmdate('Y-m-d H:i:s', $timestamp);
            }
        }
        return null;
    }

    private function normalizeBoolean(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 1;
        }
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
        if (is_numeric($value)) {
            return (int) $value !== 0 ? 1 : 0;
        }
        $normalized = strtolower(trim((string) $value));
        if (in_array($normalized, ['1', 'true', 'yes', 'y'], true)) {
            return 1;
        }
        if (in_array($normalized, ['0', 'false', 'no', 'n'], true)) {
            return 0;
        }
        return 1;
    }

    private function bulkInsert(\PDOStatement $stmt, string $sql, int $batchSize, callable $mapRow): void
    {
        $count = 0;
        $this->connection->beginTransaction();
        while ($row = $stmt->fetch()) {
            $data = $mapRow($row);
            $this->connection->executeStatement($sql, $data);
            $count++;
            if ($count % $batchSize === 0) {
                $this->connection->commit();
                $this->connection->beginTransaction();
            }
        }
        $this->connection->commit();
    }
}
