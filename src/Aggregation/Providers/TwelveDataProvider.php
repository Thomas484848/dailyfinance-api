<?php

namespace App\Aggregation\Providers;

use App\Aggregation\ParsedMetrics;
use App\Aggregation\ProviderAdapterInterface;
use App\Aggregation\ProviderLimiter;
use App\Aggregation\ProviderResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwelveDataProvider implements ProviderAdapterInterface
{
    private const BASE_URL = 'https://api.twelvedata.com';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ProviderLimiter $limiter,
        private readonly string $apiKey,
        private readonly int $ttlQuote
    ) {}

    public function getName(): string
    {
        return 'twelvedata';
    }

    public function fetchQuote(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s/quote?symbol=%s&apikey=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlQuote);
    }

    public function fetchOverview(string $symbol): ?ProviderResponse
    {
        return null;
    }

    public function fetchFinancials(string $symbol): ?ProviderResponse
    {
        return null;
    }

    public function parseQuote(mixed $payload): ?ParsedMetrics
    {
        if (!is_array($payload) || ($payload['status'] ?? null) === 'error') {
            return null;
        }
        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'price' => isset($payload['close']) ? (float) $payload['close'] : null,
                'open' => isset($payload['open']) ? (float) $payload['open'] : null,
                'high' => isset($payload['high']) ? (float) $payload['high'] : null,
                'low' => isset($payload['low']) ? (float) $payload['low'] : null,
                'close' => isset($payload['close']) ? (float) $payload['close'] : null,
                'volume' => isset($payload['volume']) ? (float) $payload['volume'] : null,
            ]
        );
    }

    public function parseOverview(mixed $payload): ?ParsedMetrics
    {
        return null;
    }

    public function parseFinancials(mixed $payload): ?ParsedMetrics
    {
        return null;
    }

    private function fetchJson(string $url): array
    {
        $this->limiter->throttle();
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'dailyfinance/1.0',
                'Accept' => 'application/json',
            ],
        ]);
        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException(sprintf('TwelveData error: %s', $response->getContent(false)));
        }
        return $response->toArray(false);
    }
}
