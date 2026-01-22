<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class JwtLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $jwt = $this->jwtManager->create($token->getUser());

        $response = new JsonResponse(['token' => $jwt], 200);

        // Cookie HttpOnly (comme tu as déjà)
        $response->headers->setCookie(
            Cookie::create('BEARER')
                ->withValue($jwt)
                ->withExpires(strtotime('+1 hour'))
                ->withPath('/')
                ->withSecure(false)      // true en prod HTTPS
                ->withHttpOnly(true)
                ->withSameSite('lax')
        );

        return $response;
    }
}
