<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ICS Importer with Category',
    'description' => 'Wraps calendarize:import and assigns a sys_category to imported/updated events',
    'category' => 'be',
    'author' => 'Schmid',
    'author_email' => '',
    'state' => 'stable',
    'version' => '1.0.0',
    'clearcacheonload' => 1,
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.99.99',
            'calendarize' => '14.0.1'
        ],
    ],
];
