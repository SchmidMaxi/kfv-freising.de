<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Katastrophenschutz-Inventar',
    'description' => 'Inventar-Katalog der KatS-Halle: Bestand, Bestellungen, Verbleib, Prüffristen',
    'category' => 'plugin',
    'state' => 'beta',
    'author' => 'Schmid',
    'author_email' => 'kontakt@maxischmid.de',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.3.0-14.9.99',
            'feuerwehren' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
