<?php

/**
 * Extension Manager/Repository config file for ext "kfv_template".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'KFV Template',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'fluid_styled_content' => '12.4.0-12.4.99',
            'rte_ckeditor' => '12.4.0-12.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'MaxiSchmid\\KfvTemplate\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Maximilian Schmid',
    'author_email' => 'kontakt@maxischmid.de',
    'author_company' => 'Maxi Schmid',
    'version' => '1.0.0',
];
