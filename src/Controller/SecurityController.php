<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;

class SecurityController
{
//    #[Route('/api/login', name: 'api_login_form', methods: ['GET'])]
//    public function loginForm(): RedirectResponse
//    {
//        return new RedirectResponse('/api/docs');
//    }

    // POST /api/login is handled by the security firewall (json_login).

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): void
    {
        throw new \LogicException('This code should never be reached. The firewall handles /api/login.');
    }
}
