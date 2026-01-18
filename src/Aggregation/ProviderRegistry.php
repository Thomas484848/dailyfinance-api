<?php

namespace App\Aggregation;

use App\Aggregation\Providers\AlphaVantageProvider;
use App\Aggregation\Providers\FinnhubProvider;
use App\Aggregation\Providers\FmpProvider;
use App\Aggregation\Providers\TwelveDataProvider;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ProviderRegistry
{
    /**
     * @return ProviderAdapterInterface[]
     */
    public function buildProviders(AggregatorConfig $config, HttpClientInterface $httpClient): array
    {
        $providers = [];

        $fmpKey = trim((string) ($_ENV['FMP_API_KEY'] ?? getenv('FMP_API_KEY') ?: ''));
        if ($fmpKey !== '') {
            $providers[] = new FmpProvider(
                $httpClient,
                $this->buildLimiter($config, 'fmp'),
                $fmpKey,
                $config->ttlSeconds['quote'],
                $config->ttlSeconds['overview']
            );
        }

        $avKey = trim((string) ($_ENV['ALPHA_VANTAGE_API_KEY'] ?? getenv('ALPHA_VANTAGE_API_KEY') ?: ''));
        if ($avKey !== '') {
            $providers[] = new AlphaVantageProvider(
                $httpClient,
                $this->buildLimiter($config, 'alphavantage'),
                $avKey,
                $config->ttlSeconds['quote'],
                $config->ttlSeconds['overview']
            );
        }

        $finnhubKey = trim((string) ($_ENV['FINNHUB_API_KEY'] ?? getenv('FINNHUB_API_KEY') ?: ''));
        if ($finnhubKey !== '') {
            $providers[] = new FinnhubProvider(
                $httpClient,
                $this->buildLimiter($config, 'finnhub'),
                $finnhubKey,
                $config->ttlSeconds['quote'],
                $config->ttlSeconds['overview'],
                $config->ttlSeconds['financials']
            );
        }

        $twelveKey = trim((string) ($_ENV['TWELVEDATA_API_KEY'] ?? getenv('TWELVEDATA_API_KEY') ?: ''));
        if ($twelveKey !== '') {
            $providers[] = new TwelveDataProvider(
                $httpClient,
                $this->buildLimiter($config, 'twelvedata'),
                $twelveKey,
                $config->ttlSeconds['quote']
            );
        }

        return $providers;
    }

    private function buildLimiter(AggregatorConfig $config, string $key): ProviderLimiter
    {
        $quota = $config->quotas[$key] ?? [];
        $rpm = $quota['requestsPerMinute'] ?? null;
        $minIntervalMs = $rpm ? (int) floor(60000 / max(1, $rpm)) : 0;
        return new ProviderLimiter($minIntervalMs);
    }
}
