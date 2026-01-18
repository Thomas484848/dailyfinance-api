<?php

namespace App\Controller;

use App\Aggregation\Orchestrator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AdminRefreshController extends AbstractController
{
    public function __construct(private readonly Orchestrator $orchestrator)
    {
    }

    #[Route('/api/admin/refresh', name: 'api_admin_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent() ?: '[]', true);
        $instrumentIds = $payload['instrumentIds'] ?? [];
        $jobType = $payload['jobType'] ?? 'manual';

        if (!is_array($instrumentIds) || $instrumentIds === []) {
            return $this->json(['error' => 'instrumentIds required'], 400);
        }

        $jobId = $this->orchestrator->refreshBatch($instrumentIds, $jobType);

        return $this->json(['jobId' => $jobId]);
    }
}
