<?php

namespace App\Service\StockImport;

final class StockUniverse
{
    public function __construct(private readonly string $universeFile)
    {
    }

    /**
     * @return list<string>
     */
    public function getSymbols(): array
    {
        $fromFile = $this->loadFromFile();
        if ($fromFile !== []) {
            return $fromFile;
        }

        return [
            'AAPL', 'MSFT', 'NVDA', 'AMZN', 'GOOGL', 'GOOG', 'META', 'TSLA', 'BRK.B', 'JPM',
            'V', 'UNH', 'XOM', 'JNJ', 'WMT', 'PG', 'MA', 'HD', 'CVX', 'ABBV',
            'KO', 'PEP', 'AVGO', 'COST', 'MRK', 'LLY', 'ORCL', 'TMO', 'ADBE', 'NKE',
            'QCOM', 'CRM', 'MCD', 'DIS', 'CSCO', 'ACN', 'TXN', 'VZ', 'PFE', 'INTC',
            'NFLX', 'AMD', 'AMGN', 'IBM', 'BA', 'CAT', 'GE', 'GS', 'MS', 'BAC',
            'C', 'WFC', 'PM', 'ABT', 'MDT', 'DHR', 'NEE', 'LIN', 'BMY', 'UPS',
            'RTX', 'LOW', 'SBUX', 'BKNG', 'INTU', 'AMAT', 'ISRG', 'AMT', 'SPGI', 'T',
            'HON', 'NOW', 'PLD', 'DE', 'LMT', 'UNP', 'BLK', 'CVS', 'GILD', 'ADP',
            'SYK', 'MDLZ', 'TJX', 'CB', 'AXP', 'SCHW', 'MO', 'FIS', 'PYPL', 'USB',
            'ELV', 'CI', 'MMM', 'BDX', 'DUK', 'SO', 'PNC', 'CSX', 'TGT', 'FDX',
        ];
    }

    /**
     * @return list<string>
     */
    private function loadFromFile(): array
    {
        if ($this->universeFile === '' || !is_file($this->universeFile)) {
            return [];
        }

        $raw = file_get_contents($this->universeFile);
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return [];
        }

        $symbols = array_values(array_filter(array_map('trim', $data), static function ($value): bool {
            return is_string($value) && $value !== '';
        }));

        return $symbols;
    }
}
