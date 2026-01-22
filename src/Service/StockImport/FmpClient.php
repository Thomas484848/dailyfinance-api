<?php

namespace App\Service\StockImport;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FmpClient implements ProviderClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(FMP_API_KEY)%')] private readonly string $apiKey,
    ) {
    }

    public function getProviderId(): string
    {
        return 'fmp';
    }

    public function fetch(string $symbol): array
    {
        if ($this->apiKey === '') {
            return [];
        }

        $symbol = strtoupper(trim($symbol));
        $profileUrl = 'https://financialmodelingprep.com/stable/profile';
        $quoteUrl = 'https://financialmodelingprep.com/stable/quote';
        $metricsUrl = 'https://financialmodelingprep.com/stable/key-metrics-ttm';
        $ratiosUrl = 'https://financialmodelingprep.com/stable/ratios-ttm';

        $profile = $this->request($profileUrl, ['symbol' => $symbol]);
        $quote = $this->request($quoteUrl, ['symbol' => $symbol]);
        $metrics = $this->request($metricsUrl, ['symbol' => $symbol]);
        $ratios = $this->request($ratiosUrl, ['symbol' => $symbol]);

        $profileItem = is_array($profile) ? ($profile[0] ?? []) : [];
        $quoteItem = is_array($quote) ? ($quote[0] ?? []) : [];
        $metricsItem = is_array($metrics) ? ($metrics[0] ?? []) : [];
        $ratiosItem = is_array($ratios) ? ($ratios[0] ?? []) : [];

        return [
            'provider' => $this->getProviderId(),
            'symbol' => $symbol,
            'name' => ValueCaster::toString($quoteItem['name'] ?? $profileItem['companyName'] ?? null),
            'exchangeCode' => ValueCaster::toString($profileItem['exchangeShortName'] ?? $quoteItem['exchange'] ?? $profileItem['exchange'] ?? null),
            'isin' => ValueCaster::toString($profileItem['isin'] ?? null),
            'currency' => ValueCaster::toString($profileItem['currency'] ?? null),
            'country' => ValueCaster::toString($profileItem['country'] ?? null),
            'sector' => ValueCaster::toString($profileItem['sector'] ?? null),
            'industry' => ValueCaster::toString($profileItem['industry'] ?? null),
            'website' => ValueCaster::toString($profileItem['website'] ?? null),
            'description' => ValueCaster::toString($profileItem['description'] ?? null),
            'logoUrl' => ValueCaster::toString($profileItem['image'] ?? null),
            'lastPrice' => ValueCaster::toFloat($quoteItem['price'] ?? $profileItem['price'] ?? null),
            'open' => ValueCaster::toFloat($quoteItem['open'] ?? null),
            'high' => ValueCaster::toFloat($quoteItem['dayHigh'] ?? null),
            'low' => ValueCaster::toFloat($quoteItem['dayLow'] ?? null),
            'prevClose' => ValueCaster::toFloat($quoteItem['previousClose'] ?? null),
            'change' => ValueCaster::toFloat($quoteItem['change'] ?? null),
            'changePercent' => ValueCaster::toFloat($quoteItem['changePercentage'] ?? $quoteItem['changesPercentage'] ?? null),
            'avgVolume30d' => ValueCaster::toFloat($quoteItem['avgVolume'] ?? $profileItem['volAvg'] ?? null),
            'marketCap' => ValueCaster::toFloat($quoteItem['marketCap'] ?? $profileItem['mktCap'] ?? null),
            'sharesOutstanding' => ValueCaster::toFloat($quoteItem['sharesOutstanding'] ?? null),
            'floatShares' => ValueCaster::toFloat($metricsItem['floatShares'] ?? null),
            'quoteTimestamp' => ValueCaster::toDateTime($quoteItem['timestamp'] ?? null),
            'beta' => ValueCaster::toFloat($profileItem['beta'] ?? null),
            'dividendYield' => ValueCaster::toFloat($ratiosItem['dividendYieldTTM'] ?? null),
            'dividendRate' => ValueCaster::toFloat($profileItem['lastDiv'] ?? $metricsItem['dividendPerShareTTM'] ?? null),
            'epsTtm' => ValueCaster::toFloat($metricsItem['epsTTM'] ?? $metricsItem['eps'] ?? $ratiosItem['epsTTM'] ?? null),
            'peTtm' => ValueCaster::toFloat($ratiosItem['priceToEarningsRatioTTM'] ?? $ratiosItem['priceEarningsRatioTTM'] ?? $metricsItem['peRatioTTM'] ?? null),
            'pb' => ValueCaster::toFloat($ratiosItem['priceToBookRatioTTM'] ?? null),
            'psTtm' => ValueCaster::toFloat($ratiosItem['priceToSalesRatioTTM'] ?? null),
            'evEbitda' => ValueCaster::toFloat($ratiosItem['enterpriseValueMultipleTTM'] ?? $ratiosItem['enterpriseValueOverEBITDATTM'] ?? null),
            'week52High' => ValueCaster::toFloat($quoteItem['yearHigh'] ?? null),
            'week52Low' => ValueCaster::toFloat($quoteItem['yearLow'] ?? null),
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
}
