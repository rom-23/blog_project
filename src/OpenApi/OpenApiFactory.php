<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {

    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
//        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
//            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
//                $openApi->getPaths()->addPath($key, $path->withGet(null));
//            };
//        }
        $openApi->getPaths()->addPath('/apiplatform/ping', new PathItem(null, 'Ping', null, new Operation('ping-id', [], [], 'Reponds')));

        $schemas               = $openApi->getComponents()->getSecuritySchemes();
        $schemas['cookieAuth'] = new \ArrayObject([
            'type' => 'apiKey',
            'in'   => 'cookie',
            'name' => 'PHPSESSID'
        ]);

        $schemas                = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type'       => 'object',
            'properties' => [
                'username' => [
                    'type'    => 'string',
                    'example' => 'john@doe.com'
                ],
                'password' => [
                    'type'    => 'string',
                    'example' => '0000'
                ]
            ]
        ]);
// retire le parametre ID de OpenApi pour /apiplatform/me
        $meOperation = $openApi->getPaths()->getPath('/apiplatform/me')->getGet()->withParameters([]);
        $mePathItem  = $openApi->getPaths()->getPath('/apiplatform/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/apiplatform/me', $mePathItem);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Utilisateur connecté',
                        'content'     => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-user.read'
                                ]
                            ]
                        ]
                    ]
                ],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                )
            ),
        );
        $openApi->getPaths()->addPath('/apiplatform/login', $pathItem);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogout',
                tags: ['Auth'],
                responses: [
                    '204' => []
                ]
            ),
        );
        $openApi->getPaths()->addPath('/logout', $pathItem);
//        $openApi               = $openApi->withSecurity(['cookieAuth' => []]);
        $openApi = $openApi->withInfo((new Model\Info('New Title', 'v2', 'Description of my custom API'))->withExtensionProperty('info-key', 'Info value'));
        $openApi = $openApi->withExtensionProperty('key', 'Custom x-key value');
        $openApi = $openApi->withExtensionProperty('x-value', 'Custom x-value value');

        return $openApi;
    }
}
