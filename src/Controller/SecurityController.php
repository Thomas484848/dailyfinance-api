<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController
{
    #[Route('/api/login', name: 'api_login_form', methods: ['GET'])]
    public function loginForm(): RedirectResponse
    {
        return new RedirectResponse('/api/docs');
    }

    // POST /api/login is handled by the security firewall (json_login).
}
