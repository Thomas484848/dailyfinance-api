<?php

namespace App\Aggregation;

final class AggregatorConfig
{
    public function __construct(
        public readonly array $ttlSeconds,
        public readonly array $priorities,
        public readonly array $quotas,
    ) {}

    public static function fromEnv(): self
    {
        $quoteTtl = self::toInt($_ENV['TTL_QUOTE_SECONDS'] ?? getenv('TTL_QUOTE_SECONDS') ?: 900, 900);
        $overviewTtl = self::toInt($_ENV['TTL_OVERVIEW_SECONDS'] ?? getenv('TTL_OVERVIEW_SECONDS') ?: 604800, 604800);
        $financialsTtl = self::toInt($_ENV['TTL_FINANCIALS_SECONDS'] ?? getenv('TTL_FINANCIALS_SECONDS') ?: 2592000, 2592000);

        return new self(
            ttlSeconds: [
                'quote' => $quoteTtl,
                'overview' => $overviewTtl,
                'financials' => $financialsTtl,
            ],
            priorities: [
                'price' => self::splitPriority($_ENV['MERGE_PRIORITY_PRICE'] ?? 'alphavantage,fmp,finnhub'),
                'marketCap' => self::splitPriority($_ENV['MERGE_PRIORITY_MARKETCAP'] ?? 'fmp,alphavantage,finnhub'),
                'pe' => self::splitPriority($_ENV['MERGE_PRIORITY_PE'] ?? 'fmp,alphavantage,finnhub'),
                'eps' => self::splitPriority($_ENV['MERGE_PRIORITY_EPS'] ?? 'fmp,alphavantage,finnhub'),
                'dividendYield' => self::splitPriority($_ENV['MERGE_PRIORITY_DIVIDEND_YIELD'] ?? 'fmp,finnhub,alphavantage'),
                'revenueTtm' => self::splitPriority($_ENV['MERGE_PRIORITY_REVENUE_TTM'] ?? 'finnhub,fmp'),
                'netIncomeTtm' => self::splitPriority($_ENV['MERGE_PRIORITY_NET_INCOME_TTM'] ?? 'finnhub,fmp'),
            ],
            quotas: [
                'fmp' => [
                    'requestsPerMinute' => self::toNullableInt($_ENV['FMP_RPM'] ?? 200),
                    'requestsPerDay' => self::toNullableInt($_ENV['FMP_RPD'] ?? 250),
                    'maxConcurrent' => self::toNullableInt($_ENV['FMP_MAX_CONCURRENT'] ?? 2),
                ],
                'alphavantage' => [
                    'requestsPerMinute' => self::toNullableInt($_ENV['AV_RPM'] ?? 5),
                    'requestsPerDay' => self::toNullableInt($_ENV['AV_RPD'] ?? 500),
                    'maxConcurrent' => self::toNullableInt($_ENV['AV_MAX_CONCURRENT'] ?? 1),
                ],
                'finnhub' => [
                    'requestsPerMinute' => self::toNullableInt($_ENV['FINNHUB_RPM'] ?? 60),
                    'requestsPerDay' => self::toNullableInt($_ENV['FINNHUB_RPD'] ?? 2000),
                    'maxConcurrent' => self::toNullableInt($_ENV['FINNHUB_MAX_CONCURRENT'] ?? 2),
                ],
                'twelvedata' => [
                    'requestsPerMinute' => self::toNullableInt($_ENV['TWELVEDATA_RPM'] ?? 8),
                    'requestsPerDay' => self::toNullableInt($_ENV['TWELVEDATA_RPD'] ?? 800),
                    'maxConcurrent' => self::toNullableInt($_ENV['TWELVEDATA_MAX_CONCURRENT'] ?? 1),
                ],
            ]
        );
    }

    private static function splitPriority(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    private static function toInt($value, int $fallback): int
    {
        $int = (int) $value;
        return $int > 0 ? $int : $fallback;
    }

    private static function toNullableInt($value): ?int
    {
        $int = (int) $value;
        return $int > 0 ? $int : null;
    }
}
