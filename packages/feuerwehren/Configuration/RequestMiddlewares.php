<?php
declare(strict_types=1);

return [
    'frontend' => [
        // Früh genug, noch vor TS-Rendering / PageResolver
        'schmid/feuerwehren/vector-tile-proxy' => [
            'target' => \Schmid\Feuerwehren\Middleware\VectorTileProxyMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/tsfe',
            ],
        ],
    ],
];
