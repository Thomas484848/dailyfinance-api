<?php

namespace App\Service\StockImport;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class StockUniverseBuilder
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(FMP_API_KEY)%')] private readonly string $fmpApiKey,
        #[Autowire('%env(TWELVEDATA_API_KEY)%')] private readonly string $twelveDataApiKey,
        private readonly StockRefreshLogger $logger,
    ) {
    }

    /**
     * @return list<string>
     */
    public function build(int $targetCount, int $etfCount): array
    {
        $symbols = [];

        $this->addSymbols($symbols, $this->fetchFmpSp500());
        $this->addSymbols($symbols, $this->fetchWikipediaNasdaq100());
        $this->addSymbols($symbols, $this->fetchWikipediaCac40());

        $etfs = $this->fetchTwelveDataEtfs($etfCount);
        $this->addSymbols($symbols, $etfs);

        $this->ensureGoldEtfs($symbols);

        if (\count($symbols) < $targetCount) {
            $this->addSymbols($symbols, $this->fetchFmpScreener($targetCount));
        }

        $symbols = array_values(array_unique($symbols));
        if (\count($symbols) > $targetCount) {
            $symbols = array_slice($symbols, 0, $targetCount);
        }

        $this->logger->info('Universe build done', [
            'count' => \count($symbols),
            'etfCount' => $etfCount,
        ]);

        return $symbols;
    }

    /**
     * @param list<string> $symbols
     * @param list<string> $toAdd
     */
    private function addSymbols(array &$symbols, array $toAdd): void
    {
        foreach ($toAdd as $symbol) {
            $symbol = strtoupper(trim($symbol));
            if ($symbol === '') {
                continue;
            }
            $symbols[] = $symbol;
        }
    }

    /**
     * @return list<string>
     */
    private function fetchFmpSp500(): array
    {
        if ($this->fmpApiKey === '') {
            return [];
        }

        $url = 'https://financialmodelingprep.com/stable/sp500-constituent';
        $payload = $this->requestJson($url, ['apikey' => $this->fmpApiKey]);
        if (!is_array($payload)) {
            return [];
        }

        $symbols = [];
        foreach ($payload as $row) {
            if (is_array($row) && isset($row['symbol'])) {
                $symbols[] = (string) $row['symbol'];
            }
        }

        $this->logger->info('Loaded S&P 500 constituents', ['count' => \count($symbols)]);

        return $symbols;
    }

    /**
     * @return list<string>
     */
    private function fetchFmpScreener(int $limit): array
    {
        if ($this->fmpApiKey === '') {
            return [];
        }

        $query = [
            'marketCapMoreThan' => 1000000000,
            'isActivelyTrading' => 'true',
            'exchange' => 'NYSE,NASDAQ,AMEX',
            'limit' => $limit,
            'apikey' => $this->fmpApiKey,
        ];

        $payload = $this->requestJson('https://financialmodelingprep.com/stable/company-screener', $query);
        if (!is_array($payload) || $payload === []) {
            $payload = $this->requestJson('https://financialmodelingprep.com/api/v3/stock-screener', $query);
        }

        if (!is_array($payload)) {
            return [];
        }

        $symbols = [];
        foreach ($payload as $row) {
            if (!is_array($row) || !isset($row['symbol'])) {
                continue;
            }
            $symbols[] = (string) $row['symbol'];
        }

        $this->logger->info('Loaded screener symbols', ['count' => \count($symbols)]);

        return $symbols;
    }

    /**
     * @return list<string>
     */
    private function fetchTwelveDataEtfs(int $limit): array
    {
        if ($this->twelveDataApiKey === '') {
            return [];
        }

        $payload = $this->requestJson('https://api.twelvedata.com/etfs/list', [
            'apikey' => $this->twelveDataApiKey,
        ]);

        if (!is_array($payload) || !isset($payload['data']) || !is_array($payload['data'])) {
            return [];
        }

        $symbols = [];
        foreach ($payload['data'] as $row) {
            if (!is_array($row) || !isset($row['symbol'])) {
                continue;
            }
            $symbols[] = (string) $row['symbol'];
            if (\count($symbols) >= $limit) {
                break;
            }
        }

        $this->logger->info('Loaded ETFs list', ['count' => \count($symbols)]);

        return $symbols;
    }

    /**
     * @return list<string>
     */
    private function fetchWikipediaNasdaq100(): array
    {
        $wikitext = $this->fetchWikipediaWikitext('Nasdaq-100');
        if ($wikitext === null) {
            return [];
        }

        preg_match_all('/\|\s*([A-Z0-9][A-Z0-9.-]{0,9})\s*\|\|/m', $wikitext, $matches);
        $symbols = array_values(array_unique($matches[1] ?? []));
        $this->logger->info('Loaded Nasdaq-100 tickers', ['count' => \count($symbols)]);

        return $symbols;
    }

    /**
     * @return list<string>
     */
    private function fetchWikipediaCac40(): array
    {
        $wikitext = $this->fetchWikipediaWikitext('CAC_40');
        if ($wikitext === null) {
            return [];
        }

        preg_match_all('/\|\s*([A-Z0-9]{1,5}\.[A-Z]{2,3})\s*\|/m', $wikitext, $matches);
        $symbols = array_values(array_unique($matches[1] ?? []));
        $this->logger->info('Loaded CAC 40 tickers', ['count' => \count($symbols)]);

        return $symbols;
    }

    private function fetchWikipediaWikitext(string $page): ?string
    {
        $payload = $this->requestJson('https://en.wikipedia.org/w/api.php', [
            'action' => 'parse',
            'page' => $page,
            'prop' => 'wikitext',
            'format' => 'json',
        ]);

        $text = $payload['parse']['wikitext']['*'] ?? null;

        return is_string($text) ? $text : null;
    }

    /**
     * @param list<string> $symbols
     */
    private function ensureGoldEtfs(array &$symbols): void
    {
        foreach (['GLD', 'IAU', 'SGOL', 'BAR'] as $etf) {
            $symbols[] = $etf;
        }
    }

    /**
     * @return array<mixed>
     */
    private function requestJson(string $url, array $query): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $query,
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => 'DailyFinance/1.0',
                ],
            ]);

            return $response->toArray(false);
        } catch (\Throwable $e) {
            $this->logger->warning('Universe request failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
