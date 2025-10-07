<?php
return [
    'ctrl' => [
        'title' => 'Feuerwehr',
        'label' => 'name',
        'searchFields' => 'name,slug,ort',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'typeicon_classes' => [
            'default' => 'tx-feuerwehren-feuerwehr'
        ],
    ],
    'columns' => [
        'hidden' => [
            'config' => [
                'type' => 'check'
            ],
        ],
        'name' => [
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required'
            ],
        ],
        'slug' => [
            'label' => 'Slug',
            'config' => [
                'type' => 'slug',
                'generatorOptions' => [
                    'fields' => [
                        'name'
                    ]
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
            ],
        ],
        'strasse' => [
            'label' => 'Straße + Hausnummer',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'plz' => [
            'label' => 'PLZ',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim'
            ],
        ],
        'ort' => [
            'label' => 'Ort',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'latitude' => [
            'label' => 'Latitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,double2',
                'default' => '0.0',
            ],
        ],
        'longitude' => [
            'label' => 'Longitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,double2',
                'default' => '0.0',
            ],
        ],
        'gruendungsdatum' => [
            'label' => 'Gründungsdatum',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'dbType' => 'date',
                'default' => null,
                'nullable' => true,
            ]
        ],
        'fahrzeugkategorien' => [
            'label' => 'Fahrzeuge',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_feuerwehren_domain_model_fahrzeugkategorie',
                'MM' => 'tx_feuerwehren_feuerwehr_fahrzeugkategorie_mm',
                'size' => 10,
                'autoSizeMax' => 30,
            ],
        ],
        'kommandant' => [
            'label' => 'Kommandant',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1,
                'items' => [['-', 0]],
            ],
        ],
        'stellv_kommandant' => [
            'label' => 'Stv. Kommandant',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1,
                'items' => [['-', 0]],
            ],
        ],
        'jugendwart' => [
            'label' => 'Jugendwart',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1,
                'items' => [['-', 0]],
            ],
        ],
        'stellv_jugendwart' => [
            'label' => 'Stv. Jugendwart',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1,
                'items' => [['-', 0]],
            ],
        ],
        'kinderwart' => [
            'label' => 'Kinderwart',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1,
                'items' => [['-', 0]],
            ],
        ],
        'stellv_kinderwart' => [
            'label' => 'Stv. Kinderwart',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1,
                'items' => [['-', 0]],
            ],
        ],
        'jubilaeen' => [
            'label' => 'Jubiläen',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_feuerwehren_domain_model_jubilaeum',
                'foreign_field' => 'feuerwehr',
                'appearance' => [
                    'useSortable' => true,
                    'collapseAll' => true,
                    'newRecordLinkTitle' => 'Jubiläum hinzufügen',
                ],
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' =>
                'name, slug, strasse, plz, ort, latitude, longitude, gruendungsdatum,
         --div--;Fahrzeuge, fahrzeugkategorien,
         --div--;Führung, kommandant, stellv_kommandant, jugendwart, stellv_jugendwart, kinderwart, stellv_kinderwart,
         --div--;Jubiläen, jubilaeen,
         --div--;Access, hidden',
        ],
    ],
];
