<?php

namespace App\Aggregation;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Orchestrator
{
    private const METRIC_COLUMN_MAP = [
        'price' => 'price',
        'open' => 'open',
        'high' => 'high',
        'low' => 'low',
        'close' => 'close',
        'volume' => 'volume',
        'marketCap' => 'market_cap',
        'pe' => 'pe',
        'eps' => 'eps',
        'dividendYield' => 'dividend_yield',
        'revenueTtm' => 'revenue_ttm',
        'netIncomeTtm' => 'net_income_ttm',
        'grossMargin' => 'gross_margin',
        'operatingMargin' => 'operating_margin',
        'profitMargin' => 'profit_margin',
        'dividendPerShare' => 'dividend_per_share',
        'payoutRatio' => 'payout_ratio',
        'revenuePerShare' => 'revenue_per_share',
        'epsDiluted' => 'eps_diluted',
        'sharesOutstanding' => 'shares_outstanding',
        'floatShares' => 'float_shares',
        'beta' => 'beta',
        'week52High' => 'week52_high',
        'week52Low' => 'week52_low',
        'avgVolume' => 'avg_volume',
        'enterpriseValue' => 'enterprise_value',
        'ebitdaTtm' => 'ebitda_ttm',
        'freeCashFlowTtm' => 'free_cash_flow_ttm',
        'operatingCashFlowTtm' => 'operating_cash_flow_ttm',
        'grossProfitTtm' => 'gross_profit_ttm',
        'totalDebt' => 'total_debt',
        'totalCash' => 'total_cash',
        'debtToEquity' => 'debt_to_equity',
        'currentRatio' => 'current_ratio',
        'quickRatio' => 'quick_ratio',
        'priceToBook' => 'price_to_book',
        'priceToSales' => 'price_to_sales',
        'pegRatio' => 'peg_ratio',
        'evToEbitda' => 'ev_to_ebitda',
        'evToRevenue' => 'ev_to_revenue',
        'bookValuePerShare' => 'book_value_per_share',
        'roa' => 'roa',
        'roe' => 'roe',
        'roi' => 'roi',
    ];

    public function __construct(
        private readonly Connection $connection,
        private readonly ProviderCacheService $cacheService,
        private readonly ProviderRegistry $providerRegistry,
        private readonly MergeMetrics $mergeMetrics,
        private readonly IdGenerator $idGenerator,
        private readonly HttpClientInterface $httpClient
    ) {}

    public function enrichInstrument(string $instrumentId, bool $force = false): ?array
    {
        $config = AggregatorConfig::fromEnv();
        $instrument = $this->connection->fetchAssociative(
            'SELECT * FROM instrument WHERE id = :id',
            ['id' => $instrumentId]
        );
        if (!$instrument) {
            return null;
        }

        $aliases = $this->connection->fetchAllAssociative(
            'SELECT provider, symbol, exchange_code FROM symbol_alias WHERE instrument_id = :id',
            ['id' => $instrumentId]
        );

        if (!$force) {
            $latest = $this->connection->fetchAssociative(
                'SELECT * FROM metrics WHERE instrument_id = :id ORDER BY as_of_date DESC LIMIT 1',
                ['id' => $instrumentId]
            );
            if ($latest) {
                $asOf = new \DateTimeImmutable($latest['as_of_date']);
                $ageSeconds = (new \DateTimeImmutable())->getTimestamp() - $asOf->getTimestamp();
                if ($ageSeconds < $config->ttlSeconds['quote']) {
                    return $this->buildMergedFromRow($latest);
                }
            }
        }

        $providers = $this->providerRegistry->buildProviders($config, $this->httpClient);
        $parsedResults = [];

        foreach ($providers as $provider) {
            $symbol = $this->resolveSymbol(
                $instrument['symbol'],
                $instrument['exchange_code'] ?? null,
                $provider->getName(),
                $aliases
            );

            try {
                $parsed = $this->getOrFetch($provider, $instrumentId, 'quote', $symbol, $config->ttlSeconds['quote']);
                if ($parsed) {
                    $parsedResults[] = ['provider' => $provider->getName(), 'parsed' => $parsed];
                }
                $parsed = $this->getOrFetch($provider, $instrumentId, 'overview', $symbol, $config->ttlSeconds['overview']);
                if ($parsed) {
                    $parsedResults[] = ['provider' => $provider->getName(), 'parsed' => $parsed];
                }
                $parsed = $this->getOrFetch($provider, $instrumentId, 'financials', $symbol, $config->ttlSeconds['financials']);
                if ($parsed) {
                    $parsedResults[] = ['provider' => $provider->getName(), 'parsed' => $parsed];
                }
            } catch (\Throwable $error) {
                continue;
            }
        }

        if ($parsedResults === []) {
            return null;
        }

        $inputs = array_map(function (array $item): array {
            return [
                'provider' => $item['provider'],
                'asOfDate' => $item['parsed']->asOfDate,
                'metrics' => $item['parsed']->metrics,
            ];
        }, $parsedResults);

        $merged = $this->mergeMetrics->merge($inputs, $config->priorities);

        $row = [
            'id' => $this->idGenerator->generate(),
            'instrument_id' => $instrumentId,
            'as_of_date' => $merged['asOfDate']->format('Y-m-d H:i:s'),
        ];

        foreach (self::METRIC_COLUMN_MAP as $metricKey => $column) {
            $row[$column] = $merged['metrics'][$metricKey] ?? null;
            $sourceKey = $metricKey . 'Source';
            $row[$column . '_source'] = $merged['sources'][$metricKey] ?? null;
        }

        $this->connection->insert('metrics', $row);

        return $merged;
    }

    public function refreshBatch(array $instrumentIds, string $jobType = 'manual'): string
    {
        $jobId = $this->idGenerator->generate();
        $this->connection->insert('job', [
            'id' => $jobId,
            'type' => $jobType,
            'status' => 'running',
            'started_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        foreach ($instrumentIds as $instrumentId) {
            $runId = $this->idGenerator->generate();
            $this->connection->insert('job_run', [
                'id' => $runId,
                'job_id' => $jobId,
                'instrument_id' => $instrumentId,
                'status' => 'running',
                'started_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);

            try {
                $this->enrichInstrument($instrumentId, true);
                $this->connection->update('job_run', [
                    'status' => 'done',
                    'finished_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                ], ['id' => $runId]);
            } catch (\Throwable $error) {
                $this->connection->update('job_run', [
                    'status' => 'error',
                    'error' => $error->getMessage(),
                    'finished_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                ], ['id' => $runId]);
            }
        }

        $this->connection->update('job', [
            'status' => 'done',
            'finished_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ], ['id' => $jobId]);

        return $jobId;
    }

    private function resolveSymbol(string $symbol, ?string $exchangeCode, string $provider, array $aliases): string
    {
        foreach ($aliases as $alias) {
            if (($alias['provider'] ?? null) !== $provider) {
                continue;
            }
            if (($alias['exchange_code'] ?? null) !== $exchangeCode) {
                continue;
            }
            if (!empty($alias['symbol'])) {
                return $alias['symbol'];
            }
        }
        return $symbol;
    }

    private function getOrFetch(
        ProviderAdapterInterface $provider,
        string $instrumentId,
        string $endpoint,
        string $symbol,
        int $ttlSeconds
    ): ?ParsedMetrics {
        $cached = $this->cacheService->getCachedPayload($instrumentId, $provider->getName(), $endpoint, $ttlSeconds);
        if ($cached !== null) {
            return match ($endpoint) {
                'quote' => $provider->parseQuote($cached),
                'overview' => $provider->parseOverview($cached),
                'financials' => $provider->parseFinancials($cached),
                default => null,
            };
        }

        $response = match ($endpoint) {
            'quote' => $provider->fetchQuote($symbol),
            'overview' => $provider->fetchOverview($symbol),
            'financials' => $provider->fetchFinancials($symbol),
            default => null,
        };

        if (!$response) {
            return null;
        }

        $this->cacheService->savePayload(
            $this->idGenerator->generate(),
            $instrumentId,
            $provider->getName(),
            $endpoint,
            $response->payload,
            $response->ttlSeconds
        );

        return match ($endpoint) {
            'quote' => $provider->parseQuote($response->payload),
            'overview' => $provider->parseOverview($response->payload),
            'financials' => $provider->parseFinancials($response->payload),
            default => null,
        };
    }

    private function buildMergedFromRow(array $row): array
    {
        $metrics = [];
        $sources = [];
        foreach (self::METRIC_COLUMN_MAP as $metricKey => $column) {
            $metrics[$metricKey] = $row[$column] ?? null;
            $sources[$metricKey] = $row[$column . '_source'] ?? null;
        }

        return [
            'asOfDate' => new \DateTimeImmutable($row['as_of_date']),
            'metrics' => $metrics,
            'sources' => $sources,
        ];
    }
}
