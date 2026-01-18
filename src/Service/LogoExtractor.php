<?php

namespace App\Service;

final class LogoExtractor
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
                    return $this->extractFromArray($item);
                }
            }
            return $this->extractFromArray($payload);
        }
        return null;
    }

    private function extractFromArray(array $data): ?string
    {
        return $data['logo']
            ?? $data['image']
            ?? $data['logoUrl']
            ?? $data['logo_url']
            ?? $data['companyLogo']
            ?? ($data['branding']['logo'] ?? null);
    }
}
