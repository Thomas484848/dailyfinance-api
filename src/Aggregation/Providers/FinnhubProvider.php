<?php

namespace App\Aggregation\Providers;

use App\Aggregation\ParsedMetrics;
use App\Aggregation\ProviderAdapterInterface;
use App\Aggregation\ProviderLimiter;
use App\Aggregation\ProviderResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FinnhubProvider implements ProviderAdapterInterface
{
    private const BASE_URL = 'https://finnhub.io/api/v1';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ProviderLimiter $limiter,
        private readonly string $apiKey,
        private readonly int $ttlQuote,
        private readonly int $ttlOverview,
        private readonly int $ttlFinancials
    ) {}

    public function getName(): string
    {
        return 'finnhub';
    }

    public function fetchQuote(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s/quote?symbol=%s&token=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlQuote);
    }

    public function fetchOverview(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s/stock/profile2?symbol=%s&token=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlOverview);
    }

    public function fetchFinancials(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s/stock/metric?symbol=%s&metric=all&token=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlFinancials);
    }

    public function parseQuote(mixed $payload): ?ParsedMetrics
    {
        if (!is_array($payload) || !array_key_exists('c', $payload)) {
            return null;
        }
        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'price' => $payload['c'] ?? null,
                'open' => $payload['o'] ?? null,
                'high' => $payload['h'] ?? null,
                'low' => $payload['l'] ?? null,
                'close' => $payload['pc'] ?? null,
            ]
        );
    }

    public function parseOverview(mixed $payload): ?ParsedMetrics
    {
        if (!is_array($payload) || empty($payload['ticker'])) {
            return null;
        }
        $marketCap = $payload['marketCapitalization'] ?? null;
        if ($marketCap !== null) {
            $marketCap = (float) $marketCap * 1000000;
        }
        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'marketCap' => $marketCap,
            ]
        );
    }

    public function parseFinancials(mixed $payload): ?ParsedMetrics
    {
        $metric = is_array($payload) ? ($payload['metric'] ?? null) : null;
        if (!is_array($metric) || isset($payload['error'])) {
            return null;
        }
        $marketCap = $metric['marketCapitalization'] ?? null;
        if ($marketCap !== null) {
            $marketCap = (float) $marketCap * 1000000;
        }
        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'pe' => $metric['peTTM'] ?? null,
                'eps' => $metric['epsTTM'] ?? null,
                'dividendYield' => $metric['dividendYieldIndicatedAnnual'] ?? null,
                'marketCap' => $marketCap,
                'beta' => $metric['beta'] ?? null,
                'sharesOutstanding' => $metric['sharesOutstanding'] ?? null,
                'week52High' => $metric['52WeekHigh'] ?? null,
                'week52Low' => $metric['52WeekLow'] ?? null,
            ]
        );
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
            throw new \RuntimeException(sprintf('Finnhub error: %s', $response->getContent(false)));
        }
        return $response->toArray(false);
    }
}
