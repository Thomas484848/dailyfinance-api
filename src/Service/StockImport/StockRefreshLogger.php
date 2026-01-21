<?php

namespace App\Service\StockImport;

final class StockRefreshLogger
{
    public function __construct(private readonly string $logFile)
    {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->write('WARN', $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function write(string $level, string $message, array $context): void
    {
        $timestamp = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $payload = $context === [] ? '' : ' | '.json_encode($context, JSON_UNESCAPED_SLASHES);
        $line = sprintf("[%s] %s %s%s\n", $timestamp, $level, $message, $payload);

        @file_put_contents($this->logFile, $line, FILE_APPEND);
    }
}
