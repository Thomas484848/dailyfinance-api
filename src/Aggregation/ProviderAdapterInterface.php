<?php

namespace App\Aggregation;

interface ProviderAdapterInterface
{
    public function getName(): string;

    public function fetchQuote(string $symbol): ?ProviderResponse;

    public function fetchOverview(string $symbol): ?ProviderResponse;

    public function fetchFinancials(string $symbol): ?ProviderResponse;

    public function parseQuote(mixed $payload): ?ParsedMetrics;

    public function parseOverview(mixed $payload): ?ParsedMetrics;

    public function parseFinancials(mixed $payload): ?ParsedMetrics;
}
