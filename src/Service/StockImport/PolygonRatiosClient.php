<?php

namespace App\Service\StockImport;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PolygonRatiosClient implements ProviderClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(POLYGON_API_KEY)%')] private readonly string $apiKey,
    ) {
    }

    public function getProviderId(): string
    {
        return 'polygon';
    }

    public function fetch(string $symbol): array
    {
        if ($this->apiKey === '') {
            return [];
        }

        $symbol = strtoupper(trim($symbol));
        $data = $this->request('https://api.polygon.io/stocks/financials/v1/ratios', [
            'ticker' => $symbol,
            'limit' => 1,
            'order' => 'desc',
        ]);

        $item = is_array($data['results'][0] ?? null) ? $data['results'][0] : [];
        if ($item === []) {
            return [];
        }

        return [
            'provider' => $this->getProviderId(),
            'symbol' => $symbol,
            'peTtm' => ValueCaster::toFloat($item['price_to_earnings'] ?? null),
            'pb' => ValueCaster::toFloat($item['price_to_book'] ?? null),
            'psTtm' => ValueCaster::toFloat($item['price_to_sales'] ?? null),
            'evEbitda' => ValueCaster::toFloat($item['ev_to_ebitda'] ?? null),
            'dividendYield' => ValueCaster::toFloat($item['dividend_yield'] ?? null),
            'epsTtm' => ValueCaster::toFloat($item['earnings_per_share'] ?? null),
            'revenueTtm' => ValueCaster::toFloat($item['revenue'] ?? null),
            'grossMargin' => ValueCaster::toFloat($item['gross_margin'] ?? null),
            'operatingMargin' => ValueCaster::toFloat($item['operating_margin'] ?? null),
            'netMargin' => ValueCaster::toFloat($item['net_margin'] ?? null),
            'freeCashFlowTtm' => ValueCaster::toFloat($item['free_cash_flow'] ?? null),
            'priceToFcf' => ValueCaster::toFloat($item['price_to_free_cash_flow'] ?? null),
            'priceToCashFlow' => ValueCaster::toFloat($item['price_to_cash_flow'] ?? null),
            'returnOnEquity' => ValueCaster::toFloat($item['return_on_equity'] ?? null),
            'returnOnAssets' => ValueCaster::toFloat($item['return_on_assets'] ?? null),
            'debtToEquity' => ValueCaster::toFloat($item['debt_to_equity'] ?? null),
            'forwardPe' => ValueCaster::toFloat($item['forward_pe'] ?? null),
            'pegRatio' => ValueCaster::toFloat($item['peg_ratio'] ?? null),
            'netDebt' => ValueCaster::toFloat($item['net_debt'] ?? null),
            'ebitdaTtm' => ValueCaster::toFloat($item['ebitda'] ?? null),
            'netDebtEbitda' => ValueCaster::toFloat($item['net_debt_to_ebitda'] ?? null),
        ];
    }

    private function request(string $url, array $query): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $query + ['apiKey' => $this->apiKey],
                'timeout' => 20,
            ]);
            return $response->toArray(false);
        } catch (\Throwable) {
            return [];
        }
    }
}
