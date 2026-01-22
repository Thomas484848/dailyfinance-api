<?php

namespace App\Service;

use App\Entity\Stock;

final class ValuationScoreService
{
    /**
     * @return array{score: float|null, label: string|null, confidence: float}
     */
    public function compute(Stock $stock): array
    {
        $weights = [
            'peTtm' => 0.30,
            'evEbitda' => 0.20,
            'pb' => 0.15,
            'psTtm' => 0.15,
            'dividendYield' => 0.05,
            'pricePosition' => 0.10,
            'beta' => 0.05,
        ];

        $scores = [];

        $pe = $stock->getPeTtm();
        if ($pe !== null && $pe > 0.0) {
            $scores['peTtm'] = $this->scoreLowerBetter($pe, [8, 15, 25, 40], [95, 80, 60, 35, 15]);
        }

        $evEbitda = $stock->getEvEbitda();
        if ($evEbitda !== null && $evEbitda > 0.0) {
            $scores['evEbitda'] = $this->scoreLowerBetter($evEbitda, [6, 10, 15, 25], [95, 80, 60, 35, 15]);
        }

        $pb = $stock->getPb();
        if ($pb !== null && $pb > 0.0) {
            $scores['pb'] = $this->scoreLowerBetter($pb, [1, 2, 4, 8], [95, 80, 60, 35, 15]);
        }

        $ps = $stock->getPsTtm();
        if ($ps !== null && $ps > 0.0) {
            $scores['psTtm'] = $this->scoreLowerBetter($ps, [1, 3, 6, 10], [90, 75, 55, 35, 15]);
        }

        $divYield = $stock->getDividendYield();
        if ($divYield !== null && $divYield >= 0.0) {
            $scores['dividendYield'] = $this->scoreDividendYield($divYield);
        }

        $price = $stock->getLastPrice();
        $weekLow = $stock->getWeek52Low();
        $weekHigh = $stock->getWeek52High();
        if ($price !== null && $weekLow !== null && $weekHigh !== null && $weekHigh > $weekLow) {
            $position = ($price - $weekLow) / ($weekHigh - $weekLow);
            $scores['pricePosition'] = $this->clamp(80.0 - (60.0 * $position), 20.0, 80.0);
        }

        $beta = $stock->getBeta();
        if ($beta !== null && $beta > 0.0) {
            $scores['beta'] = $this->scoreLowerBetter($beta, [0.8, 1.2, 1.8, 2.5], [70, 60, 45, 30, 20]);
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
            return ['score' => null, 'label' => null, 'confidence' => 0.0];
        }

        $score = $sumScore / $sumWeight;
        $confidence = $this->clamp($sumWeight / array_sum($weights), 0.0, 1.0);

        return [
            'score' => round($score, 1),
            'label' => $this->labelFromScore($score),
            'confidence' => round($confidence, 2),
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
