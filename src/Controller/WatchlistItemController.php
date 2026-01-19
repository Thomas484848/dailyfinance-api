<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\User;
use App\Entity\Watchlist;
use App\Entity\WatchlistItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class WatchlistItemController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/api/watchlists/{watchlistId}/items', name: 'api_watchlist_items_list', methods: ['GET'])]
    public function list(int $watchlistId): JsonResponse
    {
        $watchlist = $this->findOwnedWatchlist($watchlistId);
        if (!$watchlist) {
            return $this->json(['message' => 'Watchlist not found.'], 404);
        }

        $items = $this->entityManager
            ->getRepository(WatchlistItem::class)
            ->findBy(['watchlist' => $watchlist], ['position' => 'ASC', 'id' => 'ASC']);

        $data = array_map([$this, 'serializeItem'], $items);

        return $this->json(['items' => $data]);
    }

    #[Route('/api/watchlists/{watchlistId}/items', name: 'api_watchlist_items_create', methods: ['POST'])]
    public function create(Request $request, int $watchlistId): JsonResponse
    {
        $watchlist = $this->findOwnedWatchlist($watchlistId);
        if (!$watchlist) {
            return $this->json(['message' => 'Watchlist not found.'], 404);
        }

        $payload = $request->toArray();
        $stockId = isset($payload['stockId']) ? (int) $payload['stockId'] : 0;
        if ($stockId <= 0) {
            return $this->json(['message' => 'stockId is required.'], 400);
        }

        $stock = $this->entityManager->getRepository(Stock::class)->find($stockId);
        if (!$stock) {
            return $this->json(['message' => 'Stock not found.'], 404);
        }

        $item = new WatchlistItem();
        $item->setWatchlist($watchlist);
        $item->setStock($stock);
        $this->applyItemPayload($item, $payload);

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $this->json($this->serializeItem($item), 201);
    }

    #[Route('/api/watchlists/{watchlistId}/items/{itemId}', name: 'api_watchlist_items_update', methods: ['PATCH'])]
    public function update(Request $request, int $watchlistId, int $itemId): JsonResponse
    {
        $item = $this->findOwnedItem($watchlistId, $itemId);
        if (!$item) {
            return $this->json(['message' => 'Watchlist item not found.'], 404);
        }

        $payload = $request->toArray();
        $this->applyItemPayload($item, $payload);

        if (array_key_exists('stockId', $payload)) {
            $stockId = (int) $payload['stockId'];
            $stock = $this->entityManager->getRepository(Stock::class)->find($stockId);
            if (!$stock) {
                return $this->json(['message' => 'Stock not found.'], 404);
            }
            $item->setStock($stock);
        }

        $this->entityManager->flush();

        return $this->json($this->serializeItem($item));
    }

    #[Route('/api/watchlists/{watchlistId}/items/{itemId}', name: 'api_watchlist_items_delete', methods: ['DELETE'])]
    public function delete(int $watchlistId, int $itemId): JsonResponse
    {
        $item = $this->findOwnedItem($watchlistId, $itemId);
        if (!$item) {
            return $this->json(['message' => 'Watchlist item not found.'], 404);
        }

        $this->entityManager->remove($item);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    private function findOwnedWatchlist(int $watchlistId): ?Watchlist
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $this->entityManager
            ->getRepository(Watchlist::class)
            ->findOneBy(['id' => $watchlistId, 'user' => $user]);
    }

    private function findOwnedItem(int $watchlistId, int $itemId): ?WatchlistItem
    {
        $watchlist = $this->findOwnedWatchlist($watchlistId);
        if (!$watchlist) {
            return null;
        }

        return $this->entityManager
            ->getRepository(WatchlistItem::class)
            ->findOneBy(['id' => $itemId, 'watchlist' => $watchlist]);
    }

    private function applyItemPayload(WatchlistItem $item, array $payload): void
    {
        if (array_key_exists('position', $payload)) {
            $item->setPosition($payload['position'] !== null ? (int) $payload['position'] : null);
        }
        if (array_key_exists('note', $payload)) {
            $item->setNote($payload['note'] !== null ? (string) $payload['note'] : null);
        }
        if (array_key_exists('tags', $payload)) {
            $item->setTags(is_array($payload['tags']) ? $payload['tags'] : null);
        }
        if (array_key_exists('alertPriceAbove', $payload)) {
            $item->setAlertPriceAbove($payload['alertPriceAbove'] !== null ? (string) $payload['alertPriceAbove'] : null);
        }
        if (array_key_exists('alertPriceBelow', $payload)) {
            $item->setAlertPriceBelow($payload['alertPriceBelow'] !== null ? (string) $payload['alertPriceBelow'] : null);
        }
        if (array_key_exists('addedAt', $payload) && $payload['addedAt']) {
            $item->setAddedAt(new \DateTimeImmutable((string) $payload['addedAt']));
        }
    }

    private function serializeItem(WatchlistItem $item): array
    {
        $stock = $item->getStock();

        return [
            'id' => $item->getId(),
            'watchlistId' => $item->getWatchlist()?->getId(),
            'stock' => $stock ? [
                'id' => $stock->getId(),
                'symbol' => $stock->getSymbol(),
                'name' => $stock->getName(),
                'exchangeCode' => $stock->getExchangeCode(),
            ] : null,
            'addedAt' => $item->getAddedAt()->format('c'),
            'position' => $item->getPosition(),
            'note' => $item->getNote(),
            'tags' => $item->getTags(),
            'alertPriceAbove' => $item->getAlertPriceAbove(),
            'alertPriceBelow' => $item->getAlertPriceBelow(),
        ];
    }
}
