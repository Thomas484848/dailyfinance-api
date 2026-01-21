<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\StockPriceHistory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class StockHistoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(FMP_API_KEY)%')] private readonly string $fmpApiKey,
        #[Autowire('%env(FINNHUB_API_KEY)%')] private readonly string $finnhubApiKey,
    ) {
    }

    #[Route('/api/stocks/{id}/history', name: 'api_stocks_history', methods: ['GET'])]
    public function history(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Unauthorized'], 401);
        }

        $range = strtolower((string) $request->query->get('range', '1y'));
        $days = match ($range) {
            '1d' => 1,
            '1w' => 7,
            '1m' => 30,
            '6m' => 180,
            '1y' => 365,
            'max' => 3650,
            default => 365,
        };

        $stock = $this->entityManager->getRepository(Stock::class)->find($id);
        if (!$stock) {
            return $this->json(['message' => 'Stock not found.'], 404);
        }

        $to = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $from = $to->modify(sprintf('-%d days', $days));

        $historyRepo = $this->entityManager->getRepository(StockPriceHistory::class);
        $cached = $historyRepo->createQueryBuilder('h')
            ->where('h.stock = :stock')
            ->andWhere('h.timestamp >= :from')
            ->andWhere('h.timestamp <= :to')
            ->setParameters(['stock' => $stock, 'from' => $from, 'to' => $to])
            ->orderBy('h.timestamp', 'ASC')
            ->getQuery()
            ->getResult();

        $shouldRefresh = count($cached) < 20;
        if ($cached) {
            $latest = end($cached);
            if ($latest instanceof StockPriceHistory) {
                $ageSeconds = $to->getTimestamp() - $latest->getTimestamp()->getTimestamp();
                if ($ageSeconds > 6 * 3600) {
                    $shouldRefresh = true;
                }
            }
        }

        if ($shouldRefresh) {
            $points = $this->fetchHistoryFromProviders($stock->getSymbol(), $from, $to);
            if ($points) {
                $this->entityManager->createQueryBuilder()
                    ->delete(StockPriceHistory::class, 'h')
                    ->where('h.stock = :stock')
                    ->andWhere('h.timestamp >= :from')
                    ->andWhere('h.timestamp <= :to')
                    ->setParameters(['stock' => $stock, 'from' => $from, 'to' => $to])
                    ->getQuery()
                    ->execute();

                foreach ($points as $point) {
                    $row = new StockPriceHistory();
                    $row->setStock($stock);
                    $row->setTimestamp($point['timestamp']);
                    $row->setPrice($point['price']);
                    $row->setVolume($point['volume']);
                    $row->setSource($point['source']);
                    $this->entityManager->persist($row);
                }
                $this->entityManager->flush();

                $cached = $historyRepo->createQueryBuilder('h')
                    ->where('h.stock = :stock')
                    ->andWhere('h.timestamp >= :from')
                    ->andWhere('h.timestamp <= :to')
                    ->setParameters(['stock' => $stock, 'from' => $from, 'to' => $to])
                    ->orderBy('h.timestamp', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        }

        $data = array_map(static function (StockPriceHistory $row): array {
            return [
                'timestamp' => $row->getTimestamp()?->format('c'),
                'price' => $row->getPrice(),
                'volume' => $row->getVolume(),
                'source' => $row->getSource(),
            ];
        }, $cached);

        return $this->json([
            'symbol' => $stock->getSymbol(),
            'range' => $range,
            'points' => $data,
        ]);
    }

    private function fetchHistoryFromProviders(string $symbol, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $symbol = strtoupper(trim($symbol));
        $points = $this->fetchFmpHistory($symbol, $from, $to);
        if ($points) {
            return $points;
        }
        return $this->fetchFinnhubHistory($symbol, $from, $to);
    }

    private function fetchFmpHistory(string $symbol, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        if ($this->fmpApiKey === '') {
            return [];
        }
        try {
            $url = sprintf('https://financialmodelingprep.com/stable/historical-price-full/%s', $symbol);
            $response = $this->httpClient->request('GET', $url, [
                'query' => [
                    'from' => $from->format('Y-m-d'),
                    'to' => $to->format('Y-m-d'),
                    'apikey' => $this->fmpApiKey,
                ],
                'timeout' => 20,
            ]);
            $payload = $response->toArray(false);
            $historical = is_array($payload['historical'] ?? null) ? $payload['historical'] : [];
            $points = [];
            foreach ($historical as $row) {
                $date = isset($row['date']) ? new \DateTimeImmutable($row['date'], new \DateTimeZone('UTC')) : null;
                if (!$date) {
                    continue;
                }
                $points[] = [
                    'timestamp' => $date,
                    'price' => isset($row['close']) ? (float) $row['close'] : null,
                    'volume' => isset($row['volume']) ? (float) $row['volume'] : null,
                    'source' => 'fmp',
                ];
            }
            return array_reverse($points);
        } catch (\Throwable) {
            return [];
        }
    }

    private function fetchFinnhubHistory(string $symbol, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        if ($this->finnhubApiKey === '') {
            return [];
        }
        try {
            $response = $this->httpClient->request('GET', 'https://finnhub.io/api/v1/stock/candle', [
                'query' => [
                    'symbol' => $symbol,
                    'resolution' => 'D',
                    'from' => $from->getTimestamp(),
                    'to' => $to->getTimestamp(),
                    'token' => $this->finnhubApiKey,
                ],
                'timeout' => 20,
            ]);
            $payload = $response->toArray(false);
            if (($payload['s'] ?? '') !== 'ok') {
                return [];
            }
            $timestamps = $payload['t'] ?? [];
            $closes = $payload['c'] ?? [];
            $volumes = $payload['v'] ?? [];
            $points = [];
            foreach ($timestamps as $i => $ts) {
                $date = new \DateTimeImmutable('@' . $ts);
                $date = $date->setTimezone(new \DateTimeZone('UTC'));
                $points[] = [
                    'timestamp' => $date,
                    'price' => isset($closes[$i]) ? (float) $closes[$i] : null,
                    'volume' => isset($volumes[$i]) ? (float) $volumes[$i] : null,
                    'source' => 'finnhub',
                ];
            }
            return $points;
        } catch (\Throwable) {
            return [];
        }
    }
}
