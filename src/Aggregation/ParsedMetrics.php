<?php

namespace App\Aggregation;

final class ParsedMetrics
{
    public function __construct(
        public readonly \DateTimeImmutable $asOfDate,
        public readonly array $metrics,
    ) {}
}
