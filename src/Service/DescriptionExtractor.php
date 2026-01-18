<?php

namespace App\Service;

final class DescriptionExtractor
{
    public function extract(mixed $payload): ?string
    {
        if ($payload === null) {
            return null;
        }
        if (is_array($payload)) {
            if (array_is_list($payload) && count($payload) > 0) {
                $item = $payload[0];
                if (is_array($item)) {
                    return $item['description'] ?? $item['Description'] ?? null;
                }
            }
            return $payload['Description'] ?? $payload['description'] ?? null;
        }
        return null;
    }
}
