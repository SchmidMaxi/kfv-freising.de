<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'feuerwehren',
    'description' => 'Darstellung des Landkreises und der Feuerwehr-Struktur',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Schmid\\Feuerwehren\\' => 'Classes/',
        ],
    ],
];
