<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController
{
    #[Route('/api/login', name: 'api_login_form', methods: ['GET'])]
    public function loginForm(): RedirectResponse
    {
        return new RedirectResponse('/api/docs');
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return new JsonResponse(['message' => 'Invalid credentials.'], 401);
    }
}
