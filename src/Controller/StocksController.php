<?php

namespace App\Controller;

use App\Service\DescriptionExtractor;
use App\Service\LogoExtractor;
use App\Service\ValuationService;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class StocksController extends AbstractController
{
    private const ITEMS_PER_PAGE = 20;
    private const MIN_ITEMS_PER_PAGE = 10;
    private const MAX_ITEMS_PER_PAGE = 50;

    private const EXCHANGE_WHITELIST = [
        'US', 'NASDAQ', 'NYSE', 'AMEX',
        'F', 'LSE', 'PA', 'BE', 'MI', 'SW', 'ST', 'CO', 'HE', 'VI', 'LS',
        'T', 'HK', 'SS', 'SZ',
        'TO', 'AU',
    ];

    private const EXCHANGE_PRIORITY = [
        'US' => 80,
        'NYSE' => 100,
        'NASDAQ' => 95,
        'AMEX' => 90,
        'LSE' => 90,
        'PA' => 85,
        'F' => 80,
        'T' => 75,
        'HK' => 70,
        'TO' => 65,
        'SW' => 60,
        'MI' => 55,
        'BE' => 50,
        'AU' => 45,
        'SS' => 40,
        'SZ' => 35,
    ];

    public function __construct(
        private readonly Connection $connection,
        private readonly ValuationService $valuationService,
        private readonly LogoExtractor $logoExtractor,
        private readonly DescriptionExtractor $descriptionExtractor,
    ) {}

    #[Route('/api/stocks', name: 'api_stocks_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $query = trim((string) $request->query->get('query', ''));
        $country = trim((string) $request->query->get('country', ''));
        $exchange = trim((string) $request->query->get('exchange', ''));
        $status = trim((string) $request->query->get('status', ''));
        $reliableOnly = $request->query->get('reliableOnly') === '1';
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = $this->getItemsPerPage($request->query->get('perPage'));

        $where = ['i.active = true', 'i.symbol <> \'\''];
        $params = [];

        if ($country !== '') {
            $where[] = 'i.country = :country';
            $params['country'] = $country;
        }
        if ($exchange !== '') {
            $where[] = 'i.exchange_code = :exchange';
            $params['exchange'] = strtoupper($exchange);
        } else {
            $where[] = 'i.exchange_code IN (:exchanges)';
            $params['exchanges'] = self::EXCHANGE_WHITELIST;
        }
        if ($query !== '') {
            $where[] = '(i.symbol ILIKE :query OR i.name ILIKE :query)';
            $params['query'] = $query . '%';
        }

        $whereSql = implode(' AND ', $where);

        $baseSql = 'SELECT i.id, i.symbol, i.exchange_code, i.name, i.country, i.sector, i.industry, i.isin,
                           m.price, m.market_cap, m.pe
                    FROM instrument i
                    LEFT JOIN LATERAL (
                        SELECT m2.* FROM metrics m2
                        WHERE m2.instrument_id = i.id
                        ORDER BY m2.as_of_date DESC
                        LIMIT 1
                    ) m ON true
                    WHERE ' . $whereSql;

        if ($reliableOnly) {
            $baseSql .= ' AND m.market_cap >= 1000000000';
        }

        $rows = [];
        $total = 0;
        if ($status !== '') {
            $rows = $this->connection->fetchAllAssociative($baseSql . ' ORDER BY i.name ASC', $params, [
                'exchanges' => ArrayParameterType::STRING,
            ]);
        } else {
            $countSql = 'SELECT COUNT(*) FROM (' . $baseSql . ') t';
            $total = (int) $this->connection->fetchOne($countSql, $params, [
                'exchanges' => ArrayParameterType::STRING,
            ]);

            $sql = $baseSql . ' ORDER BY i.name ASC LIMIT :limit OFFSET :offset';
            $params['limit'] = $itemsPerPage;
            $params['offset'] = ($page - 1) * $itemsPerPage;

            $rows = $this->connection->fetchAllAssociative($sql, $params, [
                'exchanges' => ArrayParameterType::STRING,
                'limit' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
            ]);
        }

        $results = [];
        foreach ($rows as $row) {
            $valuation = $this->valuationService->computeValuation(
                $row['pe'] !== null ? (float) $row['pe'] : null,
                $row['sector'] ?? null
            );
            if ($status !== '' && $valuation['status'] !== $status) {
                continue;
            }

            $results[] = [
                'id' => $row['id'],
                'symbol' => $row['symbol'],
                'name' => $row['name'],
                'isin' => $row['isin'],
                'exchange' => $row['exchange_code'],
                'country' => $row['country'],
                'logoUrl' => null,
                'price' => $row['price'] !== null ? (float) $row['price'] : null,
                'marketCap' => $row['market_cap'] !== null ? (float) $row['market_cap'] : null,
                'peCurrent' => $valuation['peCurrent'],
                'peAvg' => $valuation['peAvg'],
                'status' => $valuation['status'],
            ];
        }

        if ($status !== '') {
            $total = count($results);
        }

        $offset = ($page - 1) * $itemsPerPage;
        $pageResults = array_slice($results, $offset, $itemsPerPage);
        $logos = $this->loadLogos(array_column($pageResults, 'id'));
        foreach ($pageResults as &$row) {
            $row['logoUrl'] = $logos[$row['id']] ?? null;
        }

        return $this->json([
            'stocks' => $pageResults,
            'pagination' => [
                'page' => $page,
                'itemsPerPage' => $itemsPerPage,
                'total' => $total,
                'totalPages' => (int) ceil($total / $itemsPerPage),
            ],
        ]);
    }

    #[Route('/api/stocks/{symbol}', name: 'api_stocks_show', methods: ['GET'])]
    public function show(Request $request, string $symbol): JsonResponse
    {
        $exchangeParam = $request->query->get('exchange');
        $exchange = $exchangeParam ? strtoupper((string) $exchangeParam) : null;

        $params = ['symbol' => strtoupper($symbol)];
        $sql = 'SELECT * FROM instrument WHERE symbol = :symbol';
        if ($exchange) {
            $sql .= ' AND exchange_code = :exchange';
            $params['exchange'] = $exchange;
        }

        $matches = $this->connection->fetchAllAssociative($sql, $params);
        if ($matches === []) {
            return $this->json(['error' => 'Action non trouvee'], 404);
        }

        $instrument = $exchange ? $matches[0] : $this->pickBestExchange($matches);

        $metricRows = $this->connection->fetchAllAssociative(
            'SELECT * FROM metrics WHERE instrument_id = :id ORDER BY as_of_date DESC LIMIT 120',
            ['id' => $instrument['id']]
        );

        $latest = $metricRows[0] ?? null;
        $latestWithPrice = null;
        $latestWithPe = null;
        foreach ($metricRows as $row) {
            if ($latestWithPrice === null && $row['price'] !== null) {
                $latestWithPrice = $row;
            }
            if ($latestWithPe === null && $row['pe'] !== null) {
                $latestWithPe = $row;
            }
        }

        $history = [];
        foreach (array_reverse($metricRows) as $row) {
            if ($row['price'] === null) {
                continue;
            }
            $history[] = [
                'price' => (float) $row['price'],
                'timestamp' => (new \DateTimeImmutable($row['as_of_date']))->format('c'),
            ];
        }

        $logoUrl = null;
        $description = null;
        $cacheRows = $this->connection->fetchAllAssociative(
            'SELECT payload_json FROM provider_cache
             WHERE instrument_id = :id AND endpoint = :endpoint AND provider IN (:providers)
             ORDER BY fetched_at DESC
             LIMIT 10',
            [
                'id' => $instrument['id'],
                'endpoint' => 'overview',
                'providers' => ['fmp', 'finnhub'],
            ],
            ['providers' => ArrayParameterType::STRING]
        );
        foreach ($cacheRows as $cacheRow) {
            $payload = json_decode($cacheRow['payload_json'], true);
            if (!$logoUrl) {
                $logoUrl = $this->logoExtractor->extract($payload);
            }
            if (!$description) {
                $description = $this->descriptionExtractor->extract($payload);
            }
            if ($logoUrl && $description) {
                break;
            }
        }

        $peCurrent = $latestWithPe && $latestWithPe['pe'] !== null ? (float) $latestWithPe['pe'] : null;
        $valuation = $this->valuationService->computeValuation($peCurrent, $instrument['sector'] ?? null);

        return $this->json([
            'stock' => [
                'id' => $instrument['id'],
                'symbol' => $instrument['symbol'],
                'name' => $instrument['name'],
                'logoUrl' => $logoUrl,
                'description' => $description,
                'isin' => $instrument['isin'],
                'exchange' => $instrument['exchange_code'],
                'currency' => $instrument['currency'],
                'country' => $instrument['country'],
                'sector' => $instrument['sector'],
                'industry' => $instrument['industry'],
                'marketCap' => $latest && $latest['market_cap'] !== null ? (float) $latest['market_cap'] : null,
            ],
            'quote' => $latestWithPrice && $latestWithPrice['price'] !== null
                ? [
                    'price' => (float) $latestWithPrice['price'],
                    'change' => null,
                    'changePercent' => null,
                    'timestamp' => $latestWithPrice['as_of_date'],
                ]
                : null,
            'valuation' => [
                'peCurrent' => $valuation['peCurrent'],
                'peAvg' => $valuation['peAvg'],
                'status' => $valuation['status'],
            ],
            'history' => $history,
        ]);
    }

    private function getItemsPerPage(mixed $value): int
    {
        if (!$value) {
            return self::ITEMS_PER_PAGE;
        }
        $parsed = (int) $value;
        if ($parsed <= 0) {
            return self::ITEMS_PER_PAGE;
        }
        return min(self::MAX_ITEMS_PER_PAGE, max(self::MIN_ITEMS_PER_PAGE, $parsed));
    }

    private function pickBestExchange(array $matches): array
    {
        usort($matches, function (array $a, array $b): int {
            $aPriority = $this->getExchangePriority($a['exchange_code'] ?? null);
            $bPriority = $this->getExchangePriority($b['exchange_code'] ?? null);
            if ($aPriority !== $bPriority) {
                return $bPriority <=> $aPriority;
            }
            return strcmp((string) $a['exchange_code'], (string) $b['exchange_code']);
        });
        return $matches[0];
    }

    private function getExchangePriority(?string $exchange): int
    {
        if (!$exchange) {
            return 0;
        }
        $upper = strtoupper($exchange);
        return self::EXCHANGE_PRIORITY[$upper] ?? 10;
    }

    private function loadLogos(array $instrumentIds): array
    {
        if ($instrumentIds === []) {
            return [];
        }
        $rows = $this->connection->fetchAllAssociative(
            'SELECT DISTINCT ON (instrument_id) instrument_id, payload_json
             FROM provider_cache
             WHERE instrument_id IN (:ids) AND endpoint = :endpoint AND provider IN (:providers)
             ORDER BY instrument_id, fetched_at DESC',
            [
                'ids' => $instrumentIds,
                'endpoint' => 'overview',
                'providers' => ['fmp', 'finnhub'],
            ],
            [
                'ids' => ArrayParameterType::STRING,
                'providers' => ArrayParameterType::STRING,
            ]
        );

        $logos = [];
        foreach ($rows as $row) {
            $payload = json_decode($row['payload_json'], true);
            $logo = $this->logoExtractor->extract($payload);
            if ($logo) {
                $logos[$row['instrument_id']] = $logo;
            }
        }

        return $logos;
    }
}
