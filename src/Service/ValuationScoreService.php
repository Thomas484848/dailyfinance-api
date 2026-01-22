<?php

namespace App\Service;

use App\Entity\Stock;

final class ValuationScoreService
{
    /**
     * @return array{score: float|null, label: string|null, confidence: float, breakdown: array<string, array<string, float|null>>, explain: string|null}
     */
    public function compute(Stock $stock): array
    {
        $weights = [
            'peTtm' => 0.18,
            'forwardPe' => 0.12,
            'evEbitda' => 0.12,
            'priceToFcf' => 0.10,
            'pb' => 0.08,
            'psTtm' => 0.06,
            'epsGrowthYoy' => 0.08,
            'revenueGrowthYoy' => 0.06,
            'grossMargin' => 0.05,
            'operatingMargin' => 0.05,
            'netMargin' => 0.04,
            'returnOnEquity' => 0.03,
            'debtToEquity' => 0.03,
            'netDebtEbitda' => 0.03,
            'dividendYield' => 0.03,
            'pricePosition' => 0.02,
            'beta' => 0.02,
            'pegRatio' => 0.02,
        ];

        $scores = [];
        $breakdown = [];

        $pe = $stock->getPeTtm();
        if ($pe !== null && $pe > 0.0) {
            $scores['peTtm'] = $this->scoreLowerBetter($pe, [8, 15, 25, 40], [95, 80, 60, 35, 15]);
            $breakdown['peTtm'] = ['value' => $pe, 'score' => $scores['peTtm'], 'weight' => $weights['peTtm']];
        }

        $forwardPe = $stock->getForwardPe();
        if ($forwardPe !== null && $forwardPe > 0.0) {
            $scores['forwardPe'] = $this->scoreLowerBetter($forwardPe, [8, 14, 22, 35], [95, 80, 60, 35, 15]);
            $breakdown['forwardPe'] = ['value' => $forwardPe, 'score' => $scores['forwardPe'], 'weight' => $weights['forwardPe']];
        }

        $evEbitda = $stock->getEvEbitda();
        if ($evEbitda !== null && $evEbitda > 0.0) {
            $scores['evEbitda'] = $this->scoreLowerBetter($evEbitda, [6, 10, 15, 25], [95, 80, 60, 35, 15]);
            $breakdown['evEbitda'] = ['value' => $evEbitda, 'score' => $scores['evEbitda'], 'weight' => $weights['evEbitda']];
        }

        $priceToFcf = $stock->getPriceToFcf();
        if ($priceToFcf !== null && $priceToFcf > 0.0) {
            $scores['priceToFcf'] = $this->scoreLowerBetter($priceToFcf, [10, 20, 30, 50], [90, 75, 55, 35, 15]);
            $breakdown['priceToFcf'] = ['value' => $priceToFcf, 'score' => $scores['priceToFcf'], 'weight' => $weights['priceToFcf']];
        }

        $pb = $stock->getPb();
        if ($pb !== null && $pb > 0.0) {
            $scores['pb'] = $this->scoreLowerBetter($pb, [1, 2, 4, 8], [95, 80, 60, 35, 15]);
            $breakdown['pb'] = ['value' => $pb, 'score' => $scores['pb'], 'weight' => $weights['pb']];
        }

        $ps = $stock->getPsTtm();
        if ($ps !== null && $ps > 0.0) {
            $scores['psTtm'] = $this->scoreLowerBetter($ps, [1, 3, 6, 10], [90, 75, 55, 35, 15]);
            $breakdown['psTtm'] = ['value' => $ps, 'score' => $scores['psTtm'], 'weight' => $weights['psTtm']];
        }

        $epsGrowth = $stock->getEpsGrowthYoy();
        if ($epsGrowth !== null) {
            $scores['epsGrowthYoy'] = $this->scoreHigherBetter($epsGrowth, [-0.2, 0.0, 0.1, 0.25], [15, 35, 60, 80, 95]);
            $breakdown['epsGrowthYoy'] = ['value' => $epsGrowth, 'score' => $scores['epsGrowthYoy'], 'weight' => $weights['epsGrowthYoy']];
        }

        $revGrowth = $stock->getRevenueGrowthYoy();
        if ($revGrowth !== null) {
            $scores['revenueGrowthYoy'] = $this->scoreHigherBetter($revGrowth, [-0.1, 0.0, 0.08, 0.2], [20, 40, 65, 85, 95]);
            $breakdown['revenueGrowthYoy'] = ['value' => $revGrowth, 'score' => $scores['revenueGrowthYoy'], 'weight' => $weights['revenueGrowthYoy']];
        }

        $grossMargin = $stock->getGrossMargin();
        if ($grossMargin !== null) {
            $scores['grossMargin'] = $this->scoreHigherBetter($grossMargin, [0.2, 0.35, 0.5, 0.65], [35, 55, 70, 85, 95]);
            $breakdown['grossMargin'] = ['value' => $grossMargin, 'score' => $scores['grossMargin'], 'weight' => $weights['grossMargin']];
        }

        $operatingMargin = $stock->getOperatingMargin();
        if ($operatingMargin !== null) {
            $scores['operatingMargin'] = $this->scoreHigherBetter($operatingMargin, [0.05, 0.12, 0.2, 0.3], [30, 55, 75, 90, 98]);
            $breakdown['operatingMargin'] = ['value' => $operatingMargin, 'score' => $scores['operatingMargin'], 'weight' => $weights['operatingMargin']];
        }

        $netMargin = $stock->getNetMargin();
        if ($netMargin !== null) {
            $scores['netMargin'] = $this->scoreHigherBetter($netMargin, [0.03, 0.08, 0.15, 0.25], [25, 50, 70, 88, 96]);
            $breakdown['netMargin'] = ['value' => $netMargin, 'score' => $scores['netMargin'], 'weight' => $weights['netMargin']];
        }

        $roe = $stock->getReturnOnEquity();
        if ($roe !== null) {
            $scores['returnOnEquity'] = $this->scoreHigherBetter($roe, [0.05, 0.12, 0.2, 0.3], [30, 55, 75, 90, 98]);
            $breakdown['returnOnEquity'] = ['value' => $roe, 'score' => $scores['returnOnEquity'], 'weight' => $weights['returnOnEquity']];
        }

        $debtToEquity = $stock->getDebtToEquity();
        if ($debtToEquity !== null && $debtToEquity > 0.0) {
            $scores['debtToEquity'] = $this->scoreLowerBetter($debtToEquity, [0.3, 0.8, 1.5, 2.5], [90, 75, 55, 35, 15]);
            $breakdown['debtToEquity'] = ['value' => $debtToEquity, 'score' => $scores['debtToEquity'], 'weight' => $weights['debtToEquity']];
        }

        $netDebtEbitda = $stock->getNetDebtEbitda();
        if ($netDebtEbitda !== null && $netDebtEbitda > 0.0) {
            $scores['netDebtEbitda'] = $this->scoreLowerBetter($netDebtEbitda, [1, 2, 3.5, 5], [90, 75, 55, 35, 15]);
            $breakdown['netDebtEbitda'] = ['value' => $netDebtEbitda, 'score' => $scores['netDebtEbitda'], 'weight' => $weights['netDebtEbitda']];
        }

        $peg = $stock->getPegRatio();
        if ($peg !== null && $peg > 0.0) {
            $scores['pegRatio'] = $this->scoreLowerBetter($peg, [0.7, 1.1, 1.6, 2.5], [90, 75, 55, 35, 15]);
            $breakdown['pegRatio'] = ['value' => $peg, 'score' => $scores['pegRatio'], 'weight' => $weights['pegRatio']];
        }

        $divYield = $stock->getDividendYield();
        if ($divYield !== null && $divYield >= 0.0) {
            $scores['dividendYield'] = $this->scoreDividendYield($divYield);
            $breakdown['dividendYield'] = ['value' => $divYield, 'score' => $scores['dividendYield'], 'weight' => $weights['dividendYield']];
        }

        $price = $stock->getLastPrice();
        $weekLow = $stock->getWeek52Low();
        $weekHigh = $stock->getWeek52High();
        if ($price !== null && $weekLow !== null && $weekHigh !== null && $weekHigh > $weekLow) {
            $position = ($price - $weekLow) / ($weekHigh - $weekLow);
            $scores['pricePosition'] = $this->clamp(80.0 - (60.0 * $position), 20.0, 80.0);
            $breakdown['pricePosition'] = ['value' => $position, 'score' => $scores['pricePosition'], 'weight' => $weights['pricePosition']];
        }

        $beta = $stock->getBeta();
        if ($beta !== null && $beta > 0.0) {
            $scores['beta'] = $this->scoreLowerBetter($beta, [0.8, 1.2, 1.8, 2.5], [70, 60, 45, 30, 20]);
            $breakdown['beta'] = ['value' => $beta, 'score' => $scores['beta'], 'weight' => $weights['beta']];
        }

        $sumWeight = 0.0;
        $sumScore = 0.0;
        foreach ($scores as $key => $score) {
            $weight = $weights[$key] ?? 0.0;
            if ($weight <= 0.0) {
                continue;
            }
            $sumWeight += $weight;
            $sumScore += $score * $weight;
        }

        if ($sumWeight <= 0.0) {
            return ['score' => null, 'label' => null, 'confidence' => 0.0, 'breakdown' => [], 'explain' => null];
        }

        $score = $sumScore / $sumWeight;
        $confidence = $this->clamp($sumWeight / array_sum($weights), 0.0, 1.0);

        return [
            'score' => round($score, 1),
            'label' => $this->labelFromScore($score),
            'confidence' => round($confidence, 2),
            'breakdown' => $breakdown,
            'explain' => $this->buildExplainText($stock, $score, $breakdown, $confidence),
        ];
    }

    /**
     * @param array<int, float|int> $breaks
     * @param array<int, float|int> $scores
     */
    private function scoreLowerBetter(float $value, array $breaks, array $scores): float
    {
        $count = count($breaks);
        if ($count + 1 !== count($scores)) {
            return 0.0;
        }

        if ($value <= $breaks[0]) {
            return (float) $scores[0];
        }

        for ($i = 0; $i < $count - 1; $i++) {
            $low = (float) $breaks[$i];
            $high = (float) $breaks[$i + 1];
            if ($value >= $low && $value <= $high) {
                return $this->lerp($scores[$i], $scores[$i + 1], ($value - $low) / ($high - $low));
            }
        }

        if ($value >= $breaks[$count - 1]) {
            $lastScore = (float) $scores[$count];
            return $lastScore;
        }

        return 0.0;
    }

    private function scoreDividendYield(float $yield): float
    {
        if ($yield < 0.01) {
            return 40.0;
        }
        if ($yield < 0.02) {
            return 55.0;
        }
        if ($yield < 0.04) {
            return 75.0;
        }
        if ($yield < 0.06) {
            return 70.0;
        }
        if ($yield < 0.08) {
            return 55.0;
        }
        return 40.0;
    }

    /**
     * @param array<string, array<string, float|null>> $breakdown
     */
    private function buildExplainText(Stock $stock, float $score, array $breakdown, float $confidence): string
    {
        $label = $this->labelFromScore($score);
        $labelText = match ($label) {
            'sous_evalue' => 'sous-évaluée',
            'neutre' => 'neutre',
            default => 'surévaluée',
        };

        $top = $this->topDrivers($breakdown, 3);
        $driverText = $top === []
            ? 'peu de métriques disponibles'
            : implode(', ', array_map(static fn ($d) => $d['name'] . ' ' . $d['dir'], $top));

        $confidencePct = (int) round($confidence * 100);

        $pe = $stock->getPeTtm();
        $ev = $stock->getEvEbitda();
        $growth = $stock->getEpsGrowthYoy();
        $fcf = $stock->getPriceToFcf();

        $details = [];
        if ($pe !== null) {
            $details[] = 'P/E ' . round($pe, 2);
        }
        if ($ev !== null) {
            $details[] = 'EV/EBITDA ' . round($ev, 2);
        }
        if ($growth !== null) {
            $details[] = 'Croissance EPS ' . round($growth * 100, 1) . '%';
        }
        if ($fcf !== null) {
            $details[] = 'P/FCF ' . round($fcf, 2);
        }

        $detailText = $details === [] ? '' : ' ('.implode(', ', $details).')';

        return sprintf(
            'Score %s/100 (%s, confiance %d%%) — moteurs: %s%s.',
            round($score, 1),
            $labelText,
            $confidencePct,
            $driverText,
            $detailText
        );
    }

    /**
     * @param array<string, array<string, float|null>> $breakdown
     * @return array<int, array{name: string, dir: string, impact: float}>
     */
    private function topDrivers(array $breakdown, int $limit): array
    {
        $drivers = [];
        foreach ($breakdown as $key => $row) {
            $score = $row['score'] ?? null;
            $weight = $row['weight'] ?? null;
            if ($score === null || $weight === null) {
                continue;
            }
            $impact = ((float) $score - 50.0) * (float) $weight;
            $drivers[] = [
                'name' => $this->prettyMetricName($key),
                'dir' => $impact >= 0 ? 'positif' : 'négatif',
                'impact' => abs($impact),
            ];
        }

        usort($drivers, static fn ($a, $b) => $b['impact'] <=> $a['impact']);
        return array_slice($drivers, 0, $limit);
    }

    private function prettyMetricName(string $key): string
    {
        return match ($key) {
            'peTtm' => 'P/E',
            'forwardPe' => 'Forward P/E',
            'evEbitda' => 'EV/EBITDA',
            'priceToFcf' => 'P/FCF',
            'pb' => 'P/B',
            'psTtm' => 'P/S',
            'epsGrowthYoy' => 'Croissance EPS',
            'revenueGrowthYoy' => 'Croissance CA',
            'grossMargin' => 'Marge brute',
            'operatingMargin' => 'Marge opérationnelle',
            'netMargin' => 'Marge nette',
            'returnOnEquity' => 'ROE',
            'debtToEquity' => 'Dette/Capitaux',
            'netDebtEbitda' => 'Dette nette/EBITDA',
            'pegRatio' => 'PEG',
            'dividendYield' => 'Rendement',
            'pricePosition' => 'Position 52w',
            'beta' => 'Bêta',
            default => $key,
        };
    }

    /**
     * @param array<int, float|int> $breaks
     * @param array<int, float|int> $scores
     */
    private function scoreHigherBetter(float $value, array $breaks, array $scores): float
    {
        $count = count($breaks);
        if ($count + 1 !== count($scores)) {
            return 0.0;
        }

        if ($value <= $breaks[0]) {
            return (float) $scores[0];
        }

        for ($i = 0; $i < $count - 1; $i++) {
            $low = (float) $breaks[$i];
            $high = (float) $breaks[$i + 1];
            if ($value >= $low && $value <= $high) {
                return $this->lerp($scores[$i], $scores[$i + 1], ($value - $low) / ($high - $low));
            }
        }

        if ($value >= $breaks[$count - 1]) {
            $lastScore = (float) $scores[$count];
            return $lastScore;
        }

        return 0.0;
    }

    private function labelFromScore(float $score): string
    {
        if ($score >= 70.0) {
            return 'sous_evalue';
        }
        if ($score >= 45.0) {
            return 'neutre';
        }
        return 'surevalue';
    }

    private function lerp(float|int $a, float|int $b, float $t): float
    {
        return (float) $a + ((float) $b - (float) $a) * $t;
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
