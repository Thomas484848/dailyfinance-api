<?php

namespace App\Service\StockImport;

final class StockDataMerger
{
    /**
     * @param array<string, array<string, mixed>> $dataByProvider
     * @return array<string, mixed>
     */
    public function merge(array $dataByProvider, array $priorities): array
    {
        if ($dataByProvider === []) {
            return [];
        }

        $defaultPriority = $this->normalizePriority($priorities['default'] ?? []);
        $pricePriority = $this->normalizePriority($priorities['price'] ?? []);
        $marketCapPriority = $this->normalizePriority($priorities['marketCap'] ?? []);
        $pePriority = $this->normalizePriority($priorities['peTtm'] ?? []);
        $dividendYieldPriority = $this->normalizePriority($priorities['dividendYield'] ?? []);

        $merged = [
            'priceProvider' => null,
        ];

        $merged['lastPrice'] = $this->pick($dataByProvider, $pricePriority, 'lastPrice');
        $merged['priceProvider'] = $this->pickSource($dataByProvider, $pricePriority, 'lastPrice');
        $merged['marketCap'] = $this->pick($dataByProvider, $marketCapPriority, 'marketCap');
        $merged['peTtm'] = $this->pick($dataByProvider, $pePriority, 'peTtm');
        $merged['dividendYield'] = $this->pick($dataByProvider, $dividendYieldPriority, 'dividendYield');

        $defaultFields = [
            'name',
            'exchangeCode',
            'mic',
            'isin',
            'type',
            'currency',
            'country',
            'sector',
            'industry',
            'website',
            'description',
            'logoUrl',
            'open',
            'high',
            'low',
            'prevClose',
            'change',
            'changePercent',
            'avgVolume30d',
            'sharesOutstanding',
            'floatShares',
            'quoteTimestamp',
            'beta',
            'dividendRate',
            'pb',
            'psTtm',
            'evEbitda',
            'week52High',
            'week52Low',
        ];

        foreach ($defaultFields as $field) {
            if (array_key_exists($field, $merged)) {
                continue;
            }
            $merged[$field] = $this->pick($dataByProvider, $defaultPriority, $field);
        }

        return $merged;
    }

    /**
     * @param array<string, array<string, mixed>> $dataByProvider
     * @return mixed
     */
    private function pick(array $dataByProvider, array $priority, string $field): mixed
    {
        foreach ($this->orderedProviders($dataByProvider, $priority) as $provider) {
            $value = $dataByProvider[$provider][$field] ?? null;
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, array<string, mixed>> $dataByProvider
     */
    private function pickSource(array $dataByProvider, array $priority, string $field): ?string
    {
        foreach ($this->orderedProviders($dataByProvider, $priority) as $provider) {
            $value = $dataByProvider[$provider][$field] ?? null;
            if ($value !== null && $value !== '') {
                return $provider;
            }
        }

        return null;
    }

    /**
     * @param array<string, array<string, mixed>> $dataByProvider
     * @return array<int, string>
     */
    private function orderedProviders(array $dataByProvider, array $priority): array
    {
        $available = array_keys($dataByProvider);
        $ordered = [];
        foreach ($priority as $provider) {
            if (in_array($provider, $available, true)) {
                $ordered[] = $provider;
            }
        }
        foreach ($available as $provider) {
            if (!in_array($provider, $ordered, true)) {
                $ordered[] = $provider;
            }
        }

        return $ordered;
    }

    /**
     * @param array<int, string> $priority
     * @return array<int, string>
     */
    private function normalizePriority(array $priority): array
    {
        return array_values(array_filter(array_map('trim', $priority), static fn ($value) => $value !== ''));
    }
}
