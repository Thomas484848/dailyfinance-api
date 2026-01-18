<?php

namespace App\Aggregation;

use Doctrine\DBAL\Connection;

final class ProviderCacheService
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function getCachedPayload(string $instrumentId, string $provider, string $endpoint, int $ttlSeconds): ?array
    {
        $row = $this->connection->fetchAssociative(
            'SELECT payload_json, fetched_at FROM provider_cache
             WHERE instrument_id = :instrumentId AND provider = :provider AND endpoint = :endpoint
             ORDER BY fetched_at DESC
             LIMIT 1',
            [
                'instrumentId' => $instrumentId,
                'provider' => $provider,
                'endpoint' => $endpoint,
            ]
        );

        if (!$row) {
            return null;
        }

        $fetchedAt = new \DateTimeImmutable($row['fetched_at']);
        $ageSeconds = (new \DateTimeImmutable())->getTimestamp() - $fetchedAt->getTimestamp();
        if ($ageSeconds > $ttlSeconds) {
            return null;
        }

        $payload = json_decode($row['payload_json'], true);
        return is_array($payload) ? $payload : null;
    }

    public function savePayload(
        string $id,
        string $instrumentId,
        string $provider,
        string $endpoint,
        mixed $payload,
        int $ttlSeconds
    ): void {
        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $this->connection->insert('provider_cache', [
            'id' => $id,
            'instrument_id' => $instrumentId,
            'provider' => $provider,
            'endpoint' => $endpoint,
            'payload_json' => $payloadJson,
            'fetched_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'ttl_seconds' => $ttlSeconds,
        ]);
    }
}
