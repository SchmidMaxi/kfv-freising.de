<?php
return [
    'frontend' => [
        'schmid/feuerwehren-api' => [
            'target' => \Schmid\Feuerwehren\Middleware\ApiMiddleware::class,
            // WICHTIG: Sollte vor dem Haupt-TSFE-Prozess laufen, um 404 zu verhindern.
            'before' => [
                'typo3/cms-frontend/tsfe',
            ],
            // Muss nach der Basis-Initialisierung kommen.
            'after' => [
                'typo3/cms-frontend/prepare-frame',
            ],
        ],
    ],
];