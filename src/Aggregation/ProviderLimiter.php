<?php

namespace App\Aggregation;

final class ProviderLimiter
{
    private ?float $lastRequestAt = null;

    public function __construct(private readonly int $minIntervalMs)
    {
    }

    public function throttle(): void
    {
        if ($this->minIntervalMs <= 0) {
            return;
        }

        $now = microtime(true) * 1000;
        if ($this->lastRequestAt !== null) {
            $elapsed = $now - $this->lastRequestAt;
            if ($elapsed < $this->minIntervalMs) {
                $sleepMs = $this->minIntervalMs - $elapsed;
                usleep((int) ($sleepMs * 1000));
            }
        }
        $this->lastRequestAt = microtime(true) * 1000;
    }
}
