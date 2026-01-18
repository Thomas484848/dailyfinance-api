<?php

namespace App\Aggregation\Providers;

use App\Aggregation\ParsedMetrics;
use App\Aggregation\ProviderAdapterInterface;
use App\Aggregation\ProviderLimiter;
use App\Aggregation\ProviderResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FmpProvider implements ProviderAdapterInterface
{
    private const BASE_URL = 'https://financialmodelingprep.com/stable';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ProviderLimiter $limiter,
        private readonly string $apiKey,
        private readonly int $ttlQuote,
        private readonly int $ttlOverview
    ) {}

    public function getName(): string
    {
        return 'fmp';
    }

    public function fetchQuote(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s/quote?symbol=%s&apikey=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlQuote);
    }

    public function fetchOverview(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s/profile?symbol=%s&apikey=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlOverview);
    }

    public function fetchFinancials(string $symbol): ?ProviderResponse
    {
        return null;
    }

    public function parseQuote(mixed $payload): ?ParsedMetrics
    {
        if (!is_array($payload) || count($payload) === 0) {
            return null;
        }
        $item = $payload[0] ?? [];
        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'price' => $item['price'] ?? null,
                'volume' => $item['volume'] ?? null,
                'marketCap' => $item['marketCap'] ?? ($item['mktCap'] ?? null),
                'pe' => $item['pe'] ?? null,
            ]
        );
    }

    public function parseOverview(mixed $payload): ?ParsedMetrics
    {
        if (!is_array($payload) || count($payload) === 0) {
            return null;
        }
        $item = $payload[0] ?? [];
        $week52Low = null;
        $week52High = null;
        if (isset($item['range']) && is_string($item['range']) && str_contains($item['range'], '-')) {
            [$low, $high] = array_map('trim', explode('-', $item['range']));
            $week52Low = is_numeric($low) ? (float) $low : null;
            $week52High = is_numeric($high) ? (float) $high : null;
        }

        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'marketCap' => $item['mktCap'] ?? ($item['marketCap'] ?? null),
                'pe' => $item['pe'] ?? null,
                'eps' => $item['eps'] ?? null,
                'dividendPerShare' => $item['lastDiv'] ?? null,
                'beta' => $item['beta'] ?? null,
                'avgVolume' => $item['volAvg'] ?? null,
                'sharesOutstanding' => $item['sharesOutstanding'] ?? null,
                'week52Low' => $week52Low,
                'week52High' => $week52High,
                'revenueTtm' => $item['revenue'] ?? null,
                'netIncomeTtm' => $item['netIncome'] ?? null,
                'grossMargin' => $item['grossProfitMargin'] ?? null,
                'operatingMargin' => $item['operatingProfitMargin'] ?? null,
                'profitMargin' => $item['netProfitMargin'] ?? null,
            ]
        );
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
            throw new \RuntimeException(sprintf('FMP API error: %s', $response->getContent(false)));
        }
        return $response->toArray(false);
    }
}
