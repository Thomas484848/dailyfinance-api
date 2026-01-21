<?php

namespace App\Service\StockImport;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AlphaVantageClient implements ProviderClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(ALPHA_VANTAGE_API_KEY)%')] private readonly string $apiKey,
    ) {
    }

    public function getProviderId(): string
    {
        return 'alphavantage';
    }

    public function fetch(string $symbol): array
    {
        if ($this->apiKey === '') {
            return [];
        }

        $symbol = strtoupper(trim($symbol));
        $overview = $this->request([
            'function' => 'OVERVIEW',
            'symbol' => $symbol,
        ]);

        $quote = $this->request([
            'function' => 'GLOBAL_QUOTE',
            'symbol' => $symbol,
        ]);

        if ($this->isError($overview)) {
            $overview = [];
        }
        if ($this->isError($quote)) {
            return [];
        }

        $quoteData = is_array($quote['Global Quote'] ?? null) ? $quote['Global Quote'] : [];

        return [
            'provider' => $this->getProviderId(),
            'symbol' => $symbol,
            'name' => ValueCaster::toString($overview['Name'] ?? null),
            'exchangeCode' => ValueCaster::toString($overview['Exchange'] ?? null),
            'currency' => ValueCaster::toString($overview['Currency'] ?? null),
            'country' => ValueCaster::toString($overview['Country'] ?? null),
            'sector' => ValueCaster::toString($overview['Sector'] ?? null),
            'industry' => ValueCaster::toString($overview['Industry'] ?? null),
            'description' => ValueCaster::toString($overview['Description'] ?? null),
            'lastPrice' => ValueCaster::toFloat($quoteData['05. price'] ?? null),
            'open' => ValueCaster::toFloat($quoteData['02. open'] ?? null),
            'high' => ValueCaster::toFloat($quoteData['03. high'] ?? null),
            'low' => ValueCaster::toFloat($quoteData['04. low'] ?? null),
            'prevClose' => ValueCaster::toFloat($quoteData['08. previous close'] ?? null),
            'change' => ValueCaster::toFloat($quoteData['09. change'] ?? null),
            'changePercent' => ValueCaster::toFloat($quoteData['10. change percent'] ?? null),
            'avgVolume30d' => ValueCaster::toFloat($quoteData['06. volume'] ?? null),
            'marketCap' => ValueCaster::toFloat($overview['MarketCapitalization'] ?? null),
            'sharesOutstanding' => ValueCaster::toFloat($overview['SharesOutstanding'] ?? null),
            'beta' => ValueCaster::toFloat($overview['Beta'] ?? null),
            'dividendYield' => ValueCaster::toFloat($overview['DividendYield'] ?? null),
            'dividendRate' => ValueCaster::toFloat($overview['DividendPerShare'] ?? null),
            'peTtm' => ValueCaster::toFloat($overview['PERatio'] ?? null),
            'pb' => ValueCaster::toFloat($overview['PriceToBookRatio'] ?? null),
            'psTtm' => ValueCaster::toFloat($overview['PriceToSalesRatioTTM'] ?? null),
            'evEbitda' => ValueCaster::toFloat($overview['EVToEBITDA'] ?? null),
            'week52High' => ValueCaster::toFloat($overview['52WeekHigh'] ?? null),
            'week52Low' => ValueCaster::toFloat($overview['52WeekLow'] ?? null),
        ];
    }

    private function request(array $query): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://www.alphavantage.co/query', [
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
        return isset($payload['Note']) || isset($payload['Information']) || isset($payload['Error Message']);
    }
}
