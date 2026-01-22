<?php

namespace App\Service\StockImport;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class IntrinioZacksForwardPeClient implements ProviderClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
        private readonly string $apiSecret,
    ) {
    }

    public function getProviderId(): string
    {
        return 'intrinio_zacks';
    }

    public function fetch(string $symbol): array
    {
        if ($this->apiKey === '') {
            return [];
        }

        $symbol = strtoupper(trim($symbol));
        $payload = $this->request(sprintf('https://api-v2.intrinio.com/zacks/forward_pe/%s', rawurlencode($symbol)));
        if (!is_array($payload)) {
            return [];
        }

        return [
            'provider' => $this->getProviderId(),
            'symbol' => $symbol,
            'forwardPe' => ValueCaster::toFloat($payload['forward_pe_year1'] ?? null),
            'pegRatio' => ValueCaster::toFloat($payload['forward_peg_ratio_year1'] ?? null),
            'epsTtm' => ValueCaster::toFloat($payload['last_ttm_eps'] ?? null),
        ];
    }

    private function request(string $url): array
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

            $response = $this->httpClient->request('GET', $url, $options);
            return $response->toArray(false);
        } catch (\Throwable) {
            return [];
        }
    }
}
