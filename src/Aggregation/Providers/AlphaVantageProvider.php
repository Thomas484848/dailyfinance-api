<?php

namespace App\Aggregation\Providers;

use App\Aggregation\ParsedMetrics;
use App\Aggregation\ProviderAdapterInterface;
use App\Aggregation\ProviderLimiter;
use App\Aggregation\ProviderResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AlphaVantageProvider implements ProviderAdapterInterface
{
    private const BASE_URL = 'https://www.alphavantage.co/query';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ProviderLimiter $limiter,
        private readonly string $apiKey,
        private readonly int $ttlQuote,
        private readonly int $ttlOverview
    ) {}

    public function getName(): string
    {
        return 'alphavantage';
    }

    public function fetchQuote(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s?function=GLOBAL_QUOTE&symbol=%s&apikey=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlQuote);
    }

    public function fetchOverview(string $symbol): ?ProviderResponse
    {
        $url = sprintf('%s?function=OVERVIEW&symbol=%s&apikey=%s', self::BASE_URL, urlencode($symbol), urlencode($this->apiKey));
        $payload = $this->fetchJson($url);
        return new ProviderResponse($payload, new \DateTimeImmutable(), $this->ttlOverview);
    }

    public function fetchFinancials(string $symbol): ?ProviderResponse
    {
        return null;
    }

    public function parseQuote(mixed $payload): ?ParsedMetrics
    {
        if (!is_array($payload)) {
            return null;
        }
        $quote = $payload['Global Quote'] ?? null;
        if (!is_array($quote)) {
            return null;
        }
        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'price' => isset($quote['05. price']) ? (float) $quote['05. price'] : null,
                'open' => isset($quote['02. open']) ? (float) $quote['02. open'] : null,
                'high' => isset($quote['03. high']) ? (float) $quote['03. high'] : null,
                'low' => isset($quote['04. low']) ? (float) $quote['04. low'] : null,
                'close' => isset($quote['08. previous close']) ? (float) $quote['08. previous close'] : null,
                'volume' => isset($quote['06. volume']) ? (float) $quote['06. volume'] : null,
            ]
        );
    }

    public function parseOverview(mixed $payload): ?ParsedMetrics
    {
        if (!is_array($payload)) {
            return null;
        }
        if (!isset($payload['Symbol']) || isset($payload['Information']) || isset($payload['Note']) || isset($payload['Error Message'])) {
            return null;
        }

        return new ParsedMetrics(
            new \DateTimeImmutable(),
            [
                'marketCap' => isset($payload['MarketCapitalization']) ? (float) $payload['MarketCapitalization'] : null,
                'pe' => isset($payload['PERatio']) ? (float) $payload['PERatio'] : null,
                'eps' => isset($payload['EPS']) ? (float) $payload['EPS'] : null,
                'dividendYield' => isset($payload['DividendYield']) ? (float) $payload['DividendYield'] : null,
                'dividendPerShare' => isset($payload['DividendPerShare']) ? (float) $payload['DividendPerShare'] : null,
                'payoutRatio' => isset($payload['DividendPayoutRatio']) ? (float) $payload['DividendPayoutRatio'] : null,
                'revenuePerShare' => isset($payload['RevenuePerShareTTM']) ? (float) $payload['RevenuePerShareTTM'] : null,
                'epsDiluted' => isset($payload['DilutedEPSTTM']) ? (float) $payload['DilutedEPSTTM'] : null,
                'sharesOutstanding' => isset($payload['SharesOutstanding']) ? (float) $payload['SharesOutstanding'] : null,
                'beta' => isset($payload['Beta']) ? (float) $payload['Beta'] : null,
                'week52High' => isset($payload['52WeekHigh']) ? (float) $payload['52WeekHigh'] : null,
                'week52Low' => isset($payload['52WeekLow']) ? (float) $payload['52WeekLow'] : null,
                'avgVolume' => isset($payload['AverageVolume']) ? (float) $payload['AverageVolume'] : null,
                'enterpriseValue' => isset($payload['EnterpriseValue']) ? (float) $payload['EnterpriseValue'] : null,
                'ebitdaTtm' => isset($payload['EBITDA']) ? (float) $payload['EBITDA'] : null,
                'freeCashFlowTtm' => isset($payload['FreeCashFlowTTM']) ? (float) $payload['FreeCashFlowTTM'] : null,
                'operatingCashFlowTtm' => isset($payload['OperatingCashflow']) ? (float) $payload['OperatingCashflow'] : null,
                'grossProfitTtm' => isset($payload['GrossProfitTTM']) ? (float) $payload['GrossProfitTTM'] : null,
                'totalDebt' => isset($payload['TotalDebt']) ? (float) $payload['TotalDebt'] : null,
                'totalCash' => isset($payload['TotalCash']) ? (float) $payload['TotalCash'] : null,
                'debtToEquity' => isset($payload['DebtToEquity']) ? (float) $payload['DebtToEquity'] : null,
                'currentRatio' => isset($payload['CurrentRatio']) ? (float) $payload['CurrentRatio'] : null,
                'quickRatio' => isset($payload['QuickRatio']) ? (float) $payload['QuickRatio'] : null,
                'priceToBook' => isset($payload['PriceToBookRatio']) ? (float) $payload['PriceToBookRatio'] : null,
                'priceToSales' => isset($payload['PriceToSalesRatioTTM']) ? (float) $payload['PriceToSalesRatioTTM'] : null,
                'pegRatio' => isset($payload['PEGRatio']) ? (float) $payload['PEGRatio'] : null,
                'evToEbitda' => isset($payload['EVToEBITDA']) ? (float) $payload['EVToEBITDA'] : null,
                'evToRevenue' => isset($payload['EVToRevenue']) ? (float) $payload['EVToRevenue'] : null,
                'bookValuePerShare' => isset($payload['BookValue']) ? (float) $payload['BookValue'] : null,
                'roa' => isset($payload['ReturnOnAssetsTTM']) ? (float) $payload['ReturnOnAssetsTTM'] : null,
                'roe' => isset($payload['ReturnOnEquityTTM']) ? (float) $payload['ReturnOnEquityTTM'] : null,
                'roi' => isset($payload['ReturnOnInvestmentTTM']) ? (float) $payload['ReturnOnInvestmentTTM'] : null,
                'revenueTtm' => isset($payload['RevenueTTM']) ? (float) $payload['RevenueTTM'] : null,
                'netIncomeTtm' => isset($payload['NetIncomeTTM']) ? (float) $payload['NetIncomeTTM'] : null,
                'grossMargin' => null,
                'operatingMargin' => isset($payload['OperatingMarginTTM']) ? (float) $payload['OperatingMarginTTM'] : null,
                'profitMargin' => isset($payload['ProfitMargin']) ? (float) $payload['ProfitMargin'] : null,
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
            throw new \RuntimeException(sprintf('Alpha Vantage error: %s', $response->getContent(false)));
        }
        return $response->toArray(false);
    }
}
