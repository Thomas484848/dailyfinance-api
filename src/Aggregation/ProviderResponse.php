<?php

namespace App\Aggregation;

final class ProviderResponse
{
    public function __construct(
        public readonly mixed $payload,
        public readonly \DateTimeImmutable $fetchedAt,
        public readonly int $ttlSeconds,
    ) {}
}
