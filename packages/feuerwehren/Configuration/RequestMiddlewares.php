<?php
use Schmid\Feuerwehren\Middleware\ApiGatewayMiddleware;

return [
    'frontend' => [
        'schmid/feuerwehren/api-gateway' => [
            'target' => ApiGatewayMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];