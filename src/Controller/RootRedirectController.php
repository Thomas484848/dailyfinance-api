<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class RootRedirectController
{
    #[Route('/', name: 'root_redirect', methods: ['GET'])]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse('/api/docs');
    }
}
