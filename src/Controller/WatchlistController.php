<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Watchlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class WatchlistController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/api/watchlists', name: 'api_watchlists_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Unauthorized'], 401);
        }

        $watchlists = $this->entityManager
            ->getRepository(Watchlist::class)
            ->findBy(['user' => $user], ['id' => 'ASC']);

        $data = array_map(static function (Watchlist $watchlist): array {
            return [
                'id' => $watchlist->getId(),
                'name' => $watchlist->getName(),
                'description' => $watchlist->getDescription(),
                'isDefault' => $watchlist->isDefault(),
                'createdAt' => $watchlist->getCreatedAt()?->format('c'),
                'updatedAt' => $watchlist->getUpdatedAt()?->format('c'),
            ];
        }, $watchlists);

        return $this->json(['watchlists' => $data]);
    }

    #[Route('/api/watchlists', name: 'api_watchlists_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->toArray();
        $name = isset($payload['name']) ? trim((string) $payload['name']) : '';
        if ($name === '') {
            return $this->json(['message' => 'Name is required.'], 400);
        }

        $watchlist = new Watchlist();
        $watchlist->setName($name);
        $watchlist->setDescription(isset($payload['description']) ? (string) $payload['description'] : null);
        $watchlist->setIsDefault((bool) ($payload['isDefault'] ?? false));
        $watchlist->setUser($user);

        $this->entityManager->persist($watchlist);
        $this->entityManager->flush();

        return $this->json([
            'id' => $watchlist->getId(),
            'name' => $watchlist->getName(),
            'description' => $watchlist->getDescription(),
            'isDefault' => $watchlist->isDefault(),
            'createdAt' => $watchlist->getCreatedAt()?->format('c'),
            'updatedAt' => $watchlist->getUpdatedAt()?->format('c'),
        ], 201);
    }

    #[Route('/api/watchlists/{id}', name: 'api_watchlists_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $watchlist = $this->findOwnedWatchlist($id);
        if (!$watchlist) {
            return $this->json(['message' => 'Watchlist not found.'], 404);
        }

        return $this->json([
            'id' => $watchlist->getId(),
            'name' => $watchlist->getName(),
            'description' => $watchlist->getDescription(),
            'isDefault' => $watchlist->isDefault(),
            'createdAt' => $watchlist->getCreatedAt()?->format('c'),
            'updatedAt' => $watchlist->getUpdatedAt()?->format('c'),
        ]);
    }

    #[Route('/api/watchlists/{id}', name: 'api_watchlists_update', methods: ['PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $watchlist = $this->findOwnedWatchlist($id);
        if (!$watchlist) {
            return $this->json(['message' => 'Watchlist not found.'], 404);
        }

        $payload = $request->toArray();
        if (array_key_exists('name', $payload)) {
            $name = trim((string) $payload['name']);
            if ($name === '') {
                return $this->json(['message' => 'Name cannot be empty.'], 400);
            }
            $watchlist->setName($name);
        }
        if (array_key_exists('description', $payload)) {
            $watchlist->setDescription($payload['description'] !== null ? (string) $payload['description'] : null);
        }
        if (array_key_exists('isDefault', $payload)) {
            $watchlist->setIsDefault((bool) $payload['isDefault']);
        }

        $this->entityManager->flush();

        return $this->json([
            'id' => $watchlist->getId(),
            'name' => $watchlist->getName(),
            'description' => $watchlist->getDescription(),
            'isDefault' => $watchlist->isDefault(),
            'createdAt' => $watchlist->getCreatedAt()?->format('c'),
            'updatedAt' => $watchlist->getUpdatedAt()?->format('c'),
        ]);
    }

    #[Route('/api/watchlists/{id}', name: 'api_watchlists_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $watchlist = $this->findOwnedWatchlist($id);
        if (!$watchlist) {
            return $this->json(['message' => 'Watchlist not found.'], 404);
        }

        $this->entityManager->remove($watchlist);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    private function findOwnedWatchlist(int $id): ?Watchlist
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $this->entityManager
            ->getRepository(Watchlist::class)
            ->findOneBy(['id' => $id, 'user' => $user]);
    }
}
