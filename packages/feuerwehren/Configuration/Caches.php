<?php
return [
    'feuerwehren_rate' => [
        'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
        'options' => [
            'defaultLifetime' => 60,
        ],
    ],
];