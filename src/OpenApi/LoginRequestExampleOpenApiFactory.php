<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Schema;
use ApiPlatform\OpenApi\OpenApi;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

final class LoginRequestExampleOpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly JWTTokenManagerInterface $jwtManager,
    )
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        // 1) Préparer schema + requestBody
        $schema = new Schema();
        $schema['type'] = 'object';
        $schema['properties'] = [
            'email' => ['type' => 'string'],
            'password' => ['type' => 'string'],
        ];
        $schema['required'] = ['email', 'password'];

        $example = [
            'email' => 'duarte.thomas33@gmail.com',
            'password' => 'Fermetagrandegueule0285$',
        ];

        $mediaType = (new MediaType($schema))->withExample($example);
        $content = new \ArrayObject(['application/json' => $mediaType]);
        $requestBody = new RequestBody('The login data', $content, true);

        // 2) Patch /api/login si présent
        $pathItem = $openApi->getPaths()->getPath('/api/login');
        if (null !== $pathItem && null !== $pathItem->getPost()) {
            $operation = $pathItem->getPost()->withRequestBody($requestBody);
            $openApi->getPaths()->addPath('/api/login', $pathItem->withPost($operation));
        }

        // 3) Ajouter tes endpoints custom
        $paths = $openApi->getPaths();
        $paths = $this->addEndpoint($paths, '/api/register', 'POST', 'Auth', 'Register', 'Register a new user');
        $paths = $this->addEndpoint($paths, '/api/password-reset/request', 'POST', 'Auth', 'Password Reset Request', 'Request a password reset');
        $paths = $this->addEndpoint($paths, '/api/password-reset/confirm', 'POST', 'Auth', 'Password Reset Confirm', 'Confirm password reset');
        $paths = $this->addEndpoint($paths, '/api/me/password', 'POST', 'Account', 'Change Password', 'Change current password');
        $paths = $this->addEndpoint($paths, '/api/user', 'GET', 'User', 'Get Profile', 'Get current user profile');
        $paths = $this->addEndpoint($paths, '/api/user', 'PATCH', 'User', 'Update Profile', 'Update current user profile');
        $paths = $this->addEndpoint($paths, '/api/stocks', 'GET', 'Stocks', 'List Stocks', 'List available stocks');
        $paths = $this->addEndpoint($paths, '/api/watchlists', 'GET', 'Watchlists', 'List Watchlists', 'List user watchlists');
        $paths = $this->addEndpoint($paths, '/api/watchlists', 'POST', 'Watchlists', 'Create Watchlist', 'Create a watchlist');
        $paths = $this->addEndpoint($paths, '/api/watchlists/{id}', 'GET', 'Watchlists', 'Show Watchlist', 'Get a watchlist');
        $paths = $this->addEndpoint($paths, '/api/watchlists/{id}', 'PATCH', 'Watchlists', 'Update Watchlist', 'Update a watchlist');
        $paths = $this->addEndpoint($paths, '/api/watchlists/{id}', 'DELETE', 'Watchlists', 'Delete Watchlist', 'Delete a watchlist');
        $paths = $this->addEndpoint($paths, '/api/watchlists/{watchlistId}/items', 'GET', 'Watchlist Items', 'List Watchlist Items', 'List watchlist items');
        $paths = $this->addEndpoint($paths, '/api/watchlists/{watchlistId}/items', 'POST', 'Watchlist Items', 'Create Watchlist Item', 'Add item to watchlist');
        $paths = $this->addEndpoint($paths, '/api/watchlists/{watchlistId}/items/{itemId}', 'PATCH', 'Watchlist Items', 'Update Watchlist Item', 'Update watchlist item');
        $paths = $this->addEndpoint($paths, '/api/watchlists/{watchlistId}/items/{itemId}', 'DELETE', 'Watchlist Items', 'Delete Watchlist Item', 'Delete watchlist item');

        return $openApi->withPaths($paths);
    }

    private function isAuthenticatedRequest(): bool
    {
        if (null !== $this->security->getUser()) {
            return true;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return false;
        }

        // Try several places to find a token: Authorization header, server var, query params, cookie
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader) {
            $authHeader = $request->server->get('HTTP_AUTHORIZATION');
        }

        $token = null;

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = trim(substr($authHeader, 7));
        }

        // Fallback: query parameters (e.g. ?token=xxx or ?access_token=xxx)
        if (null === $token || '' === $token) {
            $queryToken = $request->query->get('token');
            if (!$queryToken) {
                $queryToken = $request->query->get('access_token');
            }
            if ($queryToken && is_string($queryToken) && $queryToken !== '') {
                $token = $queryToken;
            }
        }

        // Fallback: cookie (BEARER) — some setups store token in cookie
        if (null === $token || '' === $token) {
            $cookieToken = $request->cookies->get('BEARER');
            if ($cookieToken && is_string($cookieToken) && $cookieToken !== '') {
                $token = $cookieToken;
            }
        }

        if (null === $token || '' === $token) {
            return false;
        }

        try {
            // parse() throws on invalid token
            $this->jwtManager->parse($token);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function addEndpoint(Paths $paths, string $path, string $method, string $tag, string $summary, string $description): Paths
    {
        $pathItem = $paths->getPath($path) ?? new PathItem();

        switch (strtoupper($method)) {
            case 'GET':
                if ($pathItem->getGet()) {
                    return $paths;
                }
                $pathItem = $pathItem->withGet($this->createOperation($tag, $summary, $description));
                break;
            case 'POST':
                if ($pathItem->getPost()) {
                    return $paths;
                }
                $pathItem = $pathItem->withPost($this->createOperation($tag, $summary, $description));
                break;
            case 'PATCH':
                if ($pathItem->getPatch()) {
                    return $paths;
                }
                $pathItem = $pathItem->withPatch($this->createOperation($tag, $summary, $description));
                break;
            case 'DELETE':
                if ($pathItem->getDelete()) {
                    return $paths;
                }
                $pathItem = $pathItem->withDelete($this->createOperation($tag, $summary, $description));
                break;
        }

        $paths->addPath($path, $pathItem);

        return $paths;
    }

    private function createOperation(string $tag, string $summary, string $description): Operation
    {
        return new Operation(
            operationId: strtolower(str_replace(' ', '_', $summary)),
            tags: [$tag],
            responses: [
                '200' => new Response('Success'),
            ],
            summary: $summary,
            description: $description
        );
    }
}
