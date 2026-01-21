<?php

namespace App\Service\StockImport;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwelveDataClient implements ProviderClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(TWELVEDATA_API_KEY)%')] private readonly string $apiKey,
    ) {
    }

    public function getProviderId(): string
    {
        return 'twelvedata';
    }

    public function fetch(string $symbol): array
    {
        if ($this->apiKey === '') {
            return [];
        }

        $symbol = strtoupper(trim($symbol));
        $quote = $this->request('https://api.twelvedata.com/quote', ['symbol' => $symbol]);
        $profile = $this->request('https://api.twelvedata.com/profile', ['symbol' => $symbol]);

        if ($this->isError($quote)) {
            return [];
        }
        if ($this->isError($profile)) {
            $profile = [];
        }

        return [
            'provider' => $this->getProviderId(),
            'symbol' => $symbol,
            'name' => ValueCaster::toString($profile['name'] ?? $quote['name'] ?? null),
            'exchangeCode' => ValueCaster::toString($profile['exchange'] ?? $quote['exchange'] ?? null),
            'mic' => ValueCaster::toString($profile['mic_code'] ?? $quote['mic_code'] ?? null),
            'currency' => ValueCaster::toString($profile['currency'] ?? $quote['currency'] ?? null),
            'country' => ValueCaster::toString($profile['country'] ?? null),
            'type' => ValueCaster::toString($profile['type'] ?? null),
            'description' => ValueCaster::toString($profile['description'] ?? null),
            'lastPrice' => ValueCaster::toFloat($quote['close'] ?? $quote['price'] ?? null),
            'open' => ValueCaster::toFloat($quote['open'] ?? null),
            'high' => ValueCaster::toFloat($quote['high'] ?? null),
            'low' => ValueCaster::toFloat($quote['low'] ?? null),
            'prevClose' => ValueCaster::toFloat($quote['previous_close'] ?? null),
            'change' => ValueCaster::toFloat($quote['change'] ?? null),
            'changePercent' => ValueCaster::toFloat($quote['percent_change'] ?? null),
            'avgVolume30d' => ValueCaster::toFloat($quote['average_volume'] ?? $quote['volume'] ?? null),
            'quoteTimestamp' => ValueCaster::toDateTime($quote['timestamp'] ?? $quote['datetime'] ?? null),
        ];
    }

    private function request(string $url, array $query): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $query + ['apikey' => $this->apiKey],
                'timeout' => 20,
            ]);
            return $response->toArray(false);
        } catch (\Throwable) {
            return [];
        }
    }

    private function isError(array $payload): bool
    {
        return isset($payload['status']) && $payload['status'] === 'error';
    }
}
