<?php
return [
    'ctrl' => [
        'title' => 'Gemeinde',
        'label' => 'name',
        'searchFields' => 'name,slug',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'typeicon_classes' => [
            'default' => 'tx-feuerwehren-gemeinde'
        ],
    ],
    'columns' => [
        'hidden' => [
            'config' => [
                'type' => 'check'
            ]
        ],
        'name' => [
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required'
            ]
        ],
        'slug' => [
            'label' => 'Slug', 'config' => [
                'type' => 'slug', 'generatorOptions' => ['fields' => ['name']], 'fallbackCharacter' => '-', 'eval' => 'uniqueInSite']
        ],
        'logo' => [
            'label' => 'Logo',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Datei hinzufügen',
                ],
            ],
        ],
        'gemeindegebiet' => [
            'label' => 'Gemeindegebiet (GeoJSON)',
            'config' => [
                'type' => 'text',
                'enableRichtext' => false,
                'rows' => 10
            ]
        ],
        'feuerwehren' => [
            'label' => 'Feuerwehren',
            'config' => [
                'type' => 'select', 'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_feuerwehren_domain_model_feuerwehr',
                'MM' => 'tx_feuerwehren_gemeinde_feuerwehr_mm', 'size' => 10, 'autoSizeMax' => 30
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'name, slug, logo, gemeindegebiet, feuerwehren, --div--;Access, hidden'
        ]
    ],
];