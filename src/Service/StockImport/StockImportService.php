<?php

namespace App\Service\StockImport;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final class StockImportService
{
    /**
     * @param iterable<ProviderClientInterface> $clients
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProviderRateLimiter $rateLimiter,
        private readonly StockDataMerger $merger,
        #[TaggedIterator('stock.provider')] private readonly iterable $clients,
        private readonly array $priorities,
        private readonly StockRefreshLogger $logger,
        private readonly \App\Service\ValuationScoreService $valuationScoreService,
    ) {
    }

    /**
     * @param callable(string, array<string, mixed>=):void|null $trace
     */
    public function importSymbol(string $symbol, ?callable $trace = null): ?Stock
    {
        $symbol = strtoupper(trim($symbol));
        if ($symbol === '') {
            return null;
        }

        $dataByProvider = [];
        if ($trace) {
            $trace('Import start', ['symbol' => $symbol]);
        }
        $this->logger->info('Import start', ['symbol' => $symbol]);
        foreach ($this->clients as $client) {
            if (!$client instanceof ProviderClientInterface) {
                continue;
            }

            $providerId = $client->getProviderId();
            if (!$this->rateLimiter->acquire($providerId)) {
                if ($trace) {
                    $trace('Provider skipped (rate limit)', [
                        'symbol' => $symbol,
                        'provider' => $providerId,
                    ]);
                }
                $this->logger->warning('Provider skipped (rate limit)', [
                    'symbol' => $symbol,
                    'provider' => $providerId,
                ]);
                continue;
            }

            if ($trace) {
                $trace('Provider request start', [
                    'symbol' => $symbol,
                    'provider' => $providerId,
                ]);
            }
            $data = $client->fetch($symbol);
            if ($data !== []) {
                $dataByProvider[$providerId] = $data;
                if ($trace) {
                    $trace('Provider data fetched', [
                        'symbol' => $symbol,
                        'provider' => $providerId,
                        'fields' => array_keys($data),
                    ]);
                }
                $this->logger->info('Provider data fetched', [
                    'symbol' => $symbol,
                    'provider' => $providerId,
                ]);
            } else {
                if ($trace) {
                    $trace('Provider returned no data', [
                        'symbol' => $symbol,
                        'provider' => $providerId,
                    ]);
                }
                $this->logger->warning('Provider returned no data', [
                    'symbol' => $symbol,
                    'provider' => $providerId,
                ]);
            }
        }

        if ($dataByProvider === []) {
            if ($trace) {
                $trace('Import skipped (no provider data)', ['symbol' => $symbol]);
            }
            $this->logger->warning('Import skipped (no provider data)', ['symbol' => $symbol]);
            return null;
        }

        $merged = $this->merger->merge($dataByProvider, $this->priorities);
        if ($trace) {
            $trace('Merged data ready', [
                'symbol' => $symbol,
                'providers' => array_keys($dataByProvider),
            ]);
        }
        $stock = $this->entityManager->getRepository(Stock::class)->findOneBy(['symbol' => $symbol]);
        if (!$stock instanceof Stock) {
            $stock = new Stock();
            $stock->setSymbol($symbol);
            if ($trace) {
                $trace('Created new Stock entity', ['symbol' => $symbol]);
            }
        }

        $this->applyMergedData($stock, $merged);
        $stock->setLastUpdatedAt(new \DateTimeImmutable());
        if (isset($merged['priceProvider']) && is_string($merged['priceProvider'])) {
            $stock->setDataSource($merged['priceProvider']);
        }
        $valuation = $this->valuationScoreService->compute($stock);
        if ($valuation['score'] !== null) {
            $stock->setValuationScore($valuation['score']);
            $stock->setValuationLabel($valuation['label']);
            $stock->setValuationConfidence($valuation['confidence']);
            $stock->setValuationBreakdown($valuation['breakdown'] ?? []);
            $stock->setValuationExplainText($valuation['explain'] ?? null);
            $stock->setValuationUpdatedAt(new \DateTimeImmutable());
        }

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        if ($trace) {
            $trace('Import done', [
                'symbol' => $symbol,
                'source' => $stock->getDataSource(),
            ]);
        }
        $this->logger->info('Import done', [
            'symbol' => $symbol,
            'source' => $stock->getDataSource(),
        ]);

        return $stock;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function applyMergedData(Stock $stock, array $data): void
    {
        $this->setIfNotNull($data, 'name', $stock->setName(...));
        $this->setIfNotNull($data, 'exchangeCode', $stock->setExchangeCode(...));
        $this->setIfNotNull($data, 'mic', $stock->setMic(...));
        $this->setIfNotNull($data, 'isin', $stock->setIsin(...));
        $this->setIfNotNull($data, 'type', $stock->setType(...));
        $this->setIfNotNull($data, 'currency', $stock->setCurrency(...));
        $this->setIfNotNull($data, 'country', $stock->setCountry(...));
        $this->setIfNotNull($data, 'sector', $stock->setSector(...));
        $this->setIfNotNull($data, 'industry', $stock->setIndustry(...));
        $this->setIfNotNull($data, 'website', $stock->setWebsite(...));
        $this->setIfNotNull($data, 'description', $stock->setDescription(...));
        $this->setIfNotNull($data, 'logoUrl', $stock->setLogoUrl(...));
        $this->setIfNotNull($data, 'lastPrice', $stock->setLastPrice(...));
        $this->setIfNotNull($data, 'open', $stock->setOpen(...));
        $this->setIfNotNull($data, 'high', $stock->setHigh(...));
        $this->setIfNotNull($data, 'low', $stock->setLow(...));
        $this->setIfNotNull($data, 'prevClose', $stock->setPrevClose(...));
        $this->setIfNotNull($data, 'change', $stock->setChange(...));
        $this->setIfNotNull($data, 'changePercent', $stock->setChangePercent(...));
        $this->setIfNotNull($data, 'avgVolume30d', $stock->setAvgVolume30d(...));
        $this->setIfNotNull($data, 'marketCap', $stock->setMarketCap(...));
        $this->setIfNotNull($data, 'sharesOutstanding', $stock->setSharesOutstanding(...));
        $this->setIfNotNull($data, 'floatShares', $stock->setFloatShares(...));
        $this->setIfNotNull($data, 'quoteTimestamp', $stock->setQuoteTimestamp(...));
        $this->setIfNotNull($data, 'beta', $stock->setBeta(...));
        $this->setIfNotNull($data, 'dividendYield', $stock->setDividendYield(...));
        $this->setIfNotNull($data, 'dividendRate', $stock->setDividendRate(...));
        $this->setIfNotNull($data, 'epsTtm', $stock->setEpsTtm(...));
        $this->setIfNotNull($data, 'peTtm', $stock->setPeTtm(...));
        $this->setIfNotNull($data, 'revenueTtm', $stock->setRevenueTtm(...));
        $this->setIfNotNull($data, 'revenueGrowthYoy', $stock->setRevenueGrowthYoy(...));
        $this->setIfNotNull($data, 'epsGrowthYoy', $stock->setEpsGrowthYoy(...));
        $this->setIfNotNull($data, 'grossMargin', $stock->setGrossMargin(...));
        $this->setIfNotNull($data, 'operatingMargin', $stock->setOperatingMargin(...));
        $this->setIfNotNull($data, 'netMargin', $stock->setNetMargin(...));
        $this->setIfNotNull($data, 'freeCashFlowTtm', $stock->setFreeCashFlowTtm(...));
        $this->setIfNotNull($data, 'ebitdaTtm', $stock->setEbitdaTtm(...));
        $this->setIfNotNull($data, 'netDebt', $stock->setNetDebt(...));
        $this->setIfNotNull($data, 'netDebtEbitda', $stock->setNetDebtEbitda(...));
        $this->setIfNotNull($data, 'priceToFcf', $stock->setPriceToFcf(...));
        $this->setIfNotNull($data, 'priceToCashFlow', $stock->setPriceToCashFlow(...));
        $this->setIfNotNull($data, 'returnOnEquity', $stock->setReturnOnEquity(...));
        $this->setIfNotNull($data, 'returnOnAssets', $stock->setReturnOnAssets(...));
        $this->setIfNotNull($data, 'debtToEquity', $stock->setDebtToEquity(...));
        $this->setIfNotNull($data, 'forwardPe', $stock->setForwardPe(...));
        $this->setIfNotNull($data, 'forwardEps', $stock->setForwardEps(...));
        $this->setIfNotNull($data, 'pegRatio', $stock->setPegRatio(...));
        $this->setIfNotNull($data, 'pb', $stock->setPb(...));
        $this->setIfNotNull($data, 'psTtm', $stock->setPsTtm(...));
        $this->setIfNotNull($data, 'evEbitda', $stock->setEvEbitda(...));
        $this->setIfNotNull($data, 'week52High', $stock->setWeek52High(...));
        $this->setIfNotNull($data, 'week52Low', $stock->setWeek52Low(...));

        if ($stock->getNetDebtEbitda() === null) {
            $netDebt = $stock->getNetDebt();
            $ebitda = $stock->getEbitdaTtm();
            if ($netDebt !== null && $ebitda !== null && $ebitda > 0.0) {
                $stock->setNetDebtEbitda($netDebt / $ebitda);
            }
        }

        if ($stock->getForwardEps() === null) {
            $forwardPe = $stock->getForwardPe();
            $price = $stock->getLastPrice();
            if ($forwardPe !== null && $forwardPe > 0.0 && $price !== null) {
                $stock->setForwardEps($price / $forwardPe);
            }
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setIfNotNull(array $data, string $key, callable $setter): void
    {
        if (!array_key_exists($key, $data)) {
            return;
        }

        $value = $data[$key];
        if ($value === null) {
            return;
        }

        $setter($value);
    }
}
