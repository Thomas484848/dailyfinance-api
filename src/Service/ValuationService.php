<?php

namespace App\Service;

final class ValuationService
{
    public const STATUS_UNDER = 'UNDER';
    public const STATUS_FAIR = 'FAIR';
    public const STATUS_OVER = 'OVER';
    public const STATUS_NA = 'NA';

    public function computeValuation(?float $peCurrent, ?string $sector): array
    {
        $peAvg = $this->generatePlaceholderPeAvg($peCurrent, $sector);
        $status = $this->calculateStatus($peCurrent, $peAvg);

        return [
            'peCurrent' => $peCurrent,
            'peAvg' => $peAvg,
            'status' => $status,
        ];
    }

    private function calculateStatus(?float $peCurrent, ?float $peAvg): string
    {
        if ($peCurrent === null || $peAvg === null || $peAvg <= 0) {
            return self::STATUS_NA;
        }

        $lower = $peAvg * 0.9;
        $upper = $peAvg * 1.1;

        if ($peCurrent < $lower) {
            return self::STATUS_UNDER;
        }
        if ($peCurrent > $upper) {
            return self::STATUS_OVER;
        }
        return self::STATUS_FAIR;
    }

    private function generatePlaceholderPeAvg(?float $peCurrent, ?string $sector): ?float
    {
        if ($peCurrent === null) {
            return null;
        }

        $sectorAverages = [
            'Technology' => 25,
            'Healthcare' => 22,
            'Financial Services' => 15,
            'Consumer Cyclical' => 18,
            'Consumer Defensive' => 20,
            'Industrials' => 17,
            'Energy' => 12,
            'Utilities' => 16,
            'Real Estate' => 35,
            'Basic Materials' => 14,
            'Communication Services' => 20,
        ];

        if ($sector && isset($sectorAverages[$sector])) {
            return $sectorAverages[$sector];
        }

        $factor = 0.95 + (abs($peCurrent * 100) % 20) / 100;
        return round($peCurrent * $factor * 100) / 100;
    }
}
