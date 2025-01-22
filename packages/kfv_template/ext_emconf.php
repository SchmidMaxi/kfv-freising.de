<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'KFV Template',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'fluid_styled_content' => '13.4.0-13.4.99',
            'rte_ckeditor' => '13.4.0-13.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Schmid\\KfvTemplate\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Maximilian Schmid',
    'author_email' => 'kontakt@maxischmid.de',
    'author_company' => 'Schmid',
    'version' => '1.0.0',
];
