<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Schema;
use ApiPlatform\OpenApi\OpenApi;

final class LoginRequestExampleOpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private readonly OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $pathItem = $openApi->getPaths()->getPath('/api/login');
        if (null === $pathItem || null === $pathItem->getPost()) {
            return $openApi;
        }

        $schema = new Schema();
        $schema['type'] = 'object';
        $schema['properties'] = [
            'email' => ['type' => 'string'],
            'password' => ['type' => 'string'],
        ];
        $schema['required'] = ['email', 'password'];

        $example = [
            'email' => 'micheldelacompta',
            'password' => 'Fermetagrandegueule0285$',
        ];

        $mediaType = (new MediaType($schema))->withExample($example);
        $content = new \ArrayObject(['application/json' => $mediaType]);
        $requestBody = new RequestBody('The login data', $content, true);

        $operation = $pathItem->getPost()->withRequestBody($requestBody);
        $pathItem = $pathItem->withPost($operation);
        $openApi->getPaths()->addPath('/api/login', $pathItem);

        return $openApi;
    }
}
