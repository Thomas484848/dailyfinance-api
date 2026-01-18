<?php

  namespace App\Controller;

  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\Routing\Annotation\Route;

  class HealthController
  {
      #[Route('/', name: 'health', methods: ['GET'])]
      public function __invoke(): JsonResponse
      {
          return new JsonResponse(['status' => 'ok']);
      }
  }
