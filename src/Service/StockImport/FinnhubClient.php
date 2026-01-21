<?php

namespace App\Service\StockImport;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FinnhubClient implements ProviderClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(FINNHUB_API_KEY)%')] private readonly string $apiKey,
    ) {
    }

    public function getProviderId(): string
    {
        return 'finnhub';
    }

    public function fetch(string $symbol): array
    {
        if ($this->apiKey === '') {
            return [];
        }

        $symbol = strtoupper(trim($symbol));
        $quote = $this->request('https://finnhub.io/api/v1/quote', ['symbol' => $symbol]);
        $profile = $this->request('https://finnhub.io/api/v1/stock/profile2', ['symbol' => $symbol]);
        $metric = $this->request('https://finnhub.io/api/v1/stock/metric', ['symbol' => $symbol, 'metric' => 'all']);

        $metrics = is_array($metric['metric'] ?? null) ? $metric['metric'] : [];

        return [
            'provider' => $this->getProviderId(),
            'symbol' => $symbol,
            'name' => ValueCaster::toString($profile['name'] ?? null),
            'exchangeCode' => ValueCaster::toString($profile['exchange'] ?? null),
            'currency' => ValueCaster::toString($profile['currency'] ?? null),
            'country' => ValueCaster::toString($profile['country'] ?? null),
            'industry' => ValueCaster::toString($profile['finnhubIndustry'] ?? null),
            'website' => ValueCaster::toString($profile['weburl'] ?? null),
            'logoUrl' => ValueCaster::toString($profile['logo'] ?? null),
            'lastPrice' => ValueCaster::toFloat($quote['c'] ?? null),
            'open' => ValueCaster::toFloat($quote['o'] ?? null),
            'high' => ValueCaster::toFloat($quote['h'] ?? null),
            'low' => ValueCaster::toFloat($quote['l'] ?? null),
            'prevClose' => ValueCaster::toFloat($quote['pc'] ?? null),
            'change' => ValueCaster::toFloat($quote['d'] ?? null),
            'changePercent' => ValueCaster::toFloat($quote['dp'] ?? null),
            'marketCap' => ValueCaster::toFloat($profile['marketCapitalization'] ?? null),
            'sharesOutstanding' => ValueCaster::toFloat($profile['shareOutstanding'] ?? null),
            'quoteTimestamp' => ValueCaster::toDateTime($quote['t'] ?? null),
            'beta' => ValueCaster::toFloat($metrics['beta'] ?? null),
            'dividendYield' => ValueCaster::toFloat($metrics['dividendYieldIndicatedAnnual'] ?? null),
            'peTtm' => ValueCaster::toFloat($metrics['peTTM'] ?? null),
            'pb' => ValueCaster::toFloat($metrics['pb'] ?? null),
            'psTtm' => ValueCaster::toFloat($metrics['psTTM'] ?? null),
            'evEbitda' => ValueCaster::toFloat($metrics['evToEbitda'] ?? null),
            'week52High' => ValueCaster::toFloat($metrics['52WeekHigh'] ?? null),
            'week52Low' => ValueCaster::toFloat($metrics['52WeekLow'] ?? null),
        ];
    }

    private function request(string $url, array $query): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $query + ['token' => $this->apiKey],
                'timeout' => 20,
            ]);
            return $response->toArray(false);
        } catch (\Throwable) {
            return [];
        }
    }
}
