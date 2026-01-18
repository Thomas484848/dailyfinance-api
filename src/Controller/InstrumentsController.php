<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class InstrumentsController extends AbstractController
{
    public function __construct(private readonly Connection $connection)
    {
    }

    #[Route('/api/instruments/search', name: 'api_instruments_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query->get('query', ''));
        if ($query === '') {
            return $this->json(['results' => []]);
        }

        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, symbol, exchange_code, name FROM instrument
             WHERE symbol ILIKE :query OR name ILIKE :query
             ORDER BY symbol ASC
             LIMIT 20',
            ['query' => $query . '%']
        );

        return $this->json([
            'results' => array_map(function (array $row): array {
                return [
                    'id' => $row['id'],
                    'symbol' => $row['symbol'],
                    'exchange' => $row['exchange_code'],
                    'name' => $row['name'],
                ];
            }, $rows),
        ]);
    }

    #[Route('/api/instruments/{id}', name: 'api_instruments_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $instrument = $this->connection->fetchAssociative(
            'SELECT * FROM instrument WHERE id = :id',
            ['id' => $id]
        );
        if (!$instrument) {
            return $this->json(['error' => 'Instrument not found'], 404);
        }

        $metrics = $this->connection->fetchAssociative(
            'SELECT * FROM metrics WHERE instrument_id = :id ORDER BY as_of_date DESC LIMIT 1',
            ['id' => $id]
        );

        return $this->json([
            'instrument' => $instrument,
            'metrics' => $metrics ?: null,
        ]);
    }
}
