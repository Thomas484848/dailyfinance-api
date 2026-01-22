<?php

namespace App\Service\StockImport;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class IntrinioDataPointClient implements ProviderClientInterface
{
    /**
     * @param array<string, string> $tagMap
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
        private readonly string $apiSecret,
        private readonly array $tagMap,
    ) {
    }

    public function getProviderId(): string
    {
        return 'intrinio';
    }

    public function fetch(string $symbol): array
    {
        if ($this->apiKey === '') {
            return [];
        }

        $symbol = strtoupper(trim($symbol));
        $data = [
            'provider' => $this->getProviderId(),
            'symbol' => $symbol,
        ];

        $totalDebt = null;
        $cash = null;

        foreach ($this->tagMap as $field => $tag) {
            if ($tag === '') {
                continue;
            }
            $value = $this->fetchDataPoint($symbol, $tag);
            if ($value === null) {
                continue;
            }

            if ($field === 'totalDebt') {
                $totalDebt = $value;
                continue;
            }
            if ($field === 'cashAndEquivalents') {
                $cash = $value;
                continue;
            }

            $data[$field] = $value;
        }

        if (!array_key_exists('netDebt', $data) && $totalDebt !== null && $cash !== null) {
            $data['netDebt'] = $totalDebt - $cash;
        }

        return $data;
    }

    private function fetchDataPoint(string $symbol, string $tag): ?float
    {
        try {
            $options = [
                'timeout' => 20,
            ];
            if ($this->apiSecret !== '') {
                $options['auth_basic'] = [$this->apiKey, $this->apiSecret];
            } else {
                $options['query'] = ['api_key' => $this->apiKey];
            }

            $response = $this->httpClient->request(
                'GET',
                sprintf('https://api-v2.intrinio.com/data_point/%s/%s/number', rawurlencode($symbol), rawurlencode($tag)),
                $options
            );
            $payload = $response->toArray(false);
            return ValueCaster::toFloat($payload['value'] ?? null);
        } catch (\Throwable) {
            return null;
        }
    }
}
