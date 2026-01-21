<?php

namespace App\Service\StockImport;

final class ProviderRateLimiter
{
    /**
     * @var array<string, array{rpm:int, rpd:int, maxConcurrent:int}>
     */
    private array $limits;

    /**
     * @var array<string, float>
     */
    private array $lastRequestAt = [];

    /**
     * @var array<string, int>
     */
    private array $dailyCount = [];

    /**
     * @var array<string, string>
     */
    private array $dailyKey = [];

    /**
     * @param array<string, array{rpm:int, rpd:int, maxConcurrent:int}> $limits
     */
    public function __construct(array $limits, private readonly ?StockRefreshLogger $logger = null)
    {
        $this->limits = $limits;
    }

    public function acquire(string $provider): bool
    {
        if (!isset($this->limits[$provider])) {
            return true;
        }

        $limit = $this->limits[$provider];
        $today = (new \DateTimeImmutable('today'))->format('Y-m-d');

        if (($this->dailyKey[$provider] ?? null) !== $today) {
            $this->dailyKey[$provider] = $today;
            $this->dailyCount[$provider] = 0;
        }

        $rpd = $limit['rpd'];
        if ($rpd > 0 && ($this->dailyCount[$provider] ?? 0) >= $rpd) {
            if ($this->logger) {
                $this->logger->warning('Rate limit reached for provider (RPD)', [
                    'provider' => $provider,
                    'rpd' => $rpd,
                ]);
            }
            return false;
        }

        $rpm = $limit['rpm'];
        if ($rpm > 0) {
            $minInterval = 60.0 / $rpm;
            $lastAt = $this->lastRequestAt[$provider] ?? 0.0;
            $elapsed = microtime(true) - $lastAt;
            if ($elapsed < $minInterval) {
                $sleepSeconds = $minInterval - $elapsed;
                usleep((int) round($sleepSeconds * 1_000_000));
            }
        }

        $this->lastRequestAt[$provider] = microtime(true);
        $this->dailyCount[$provider] = ($this->dailyCount[$provider] ?? 0) + 1;

        return true;
    }
}
