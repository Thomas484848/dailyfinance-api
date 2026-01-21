<?php

namespace App\Service\StockImport;

interface ProviderClientInterface
{
    public function getProviderId(): string;

    /**
     * @return array<string, mixed>
     */
    public function fetch(string $symbol): array;
}
