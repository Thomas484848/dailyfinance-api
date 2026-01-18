<?php

namespace App\Aggregation;

final class MergeMetrics
{
    private const METRIC_KEYS = [
        'price',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'marketCap',
        'pe',
        'eps',
        'dividendYield',
        'dividendPerShare',
        'payoutRatio',
        'revenuePerShare',
        'epsDiluted',
        'sharesOutstanding',
        'floatShares',
        'beta',
        'week52High',
        'week52Low',
        'avgVolume',
        'enterpriseValue',
        'ebitdaTtm',
        'freeCashFlowTtm',
        'operatingCashFlowTtm',
        'grossProfitTtm',
        'totalDebt',
        'totalCash',
        'debtToEquity',
        'currentRatio',
        'quickRatio',
        'priceToBook',
        'priceToSales',
        'pegRatio',
        'evToEbitda',
        'evToRevenue',
        'bookValuePerShare',
        'roa',
        'roe',
        'roi',
        'revenueTtm',
        'netIncomeTtm',
        'grossMargin',
        'operatingMargin',
        'profitMargin',
    ];

    public function merge(array $inputs, array $priorities): array
    {
        $metrics = [];
        $sources = [];
        $latest = new \DateTimeImmutable('@0');

        foreach ($inputs as $input) {
            if ($input['asOfDate'] > $latest) {
                $latest = $input['asOfDate'];
            }
        }

        foreach (self::METRIC_KEYS as $key) {
            $candidates = [];
            foreach ($inputs as $input) {
                $value = $input['metrics'][$key] ?? null;
                if ($value === null || is_nan((float) $value)) {
                    continue;
                }
                $candidates[] = [
                    'provider' => $input['provider'],
                    'asOfDate' => $input['asOfDate'],
                    'value' => $value,
                ];
            }

            if ($candidates === []) {
                $metrics[$key] = null;
                $sources[$key] = null;
                continue;
            }

            $priorityList = $priorities[$key] ?? [];
            usort($candidates, function (array $a, array $b) use ($priorityList): int {
                $aPriority = array_search($a['provider'], $priorityList, true);
                $bPriority = array_search($b['provider'], $priorityList, true);
                $aRank = $aPriority === false ? PHP_INT_MAX : $aPriority;
                $bRank = $bPriority === false ? PHP_INT_MAX : $bPriority;
                if ($aRank !== $bRank) {
                    return $aRank <=> $bRank;
                }
                return $b['asOfDate'] <=> $a['asOfDate'];
            });

            $metrics[$key] = $candidates[0]['value'];
            $sources[$key] = $candidates[0]['provider'];
        }

        if ($latest->getTimestamp() === 0) {
            $latest = new \DateTimeImmutable();
        }

        return [
            'metrics' => $metrics,
            'sources' => $sources,
            'asOfDate' => $latest,
        ];
    }
}
