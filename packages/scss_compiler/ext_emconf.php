<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'SCSS Compiler',
    'description' => 'Compile scss files with scssphp/scssphp across different extensions',
    'category' => 'plugin',
    'author' => 'Manuel Schnabel',
    'author_email' => 'manuel.schnabel@trionline.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '12.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
