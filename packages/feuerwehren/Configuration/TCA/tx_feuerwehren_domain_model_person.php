<?php
return [
    'ctrl' => [
        'title' => 'Person',
        'label' => 'title',
        'searchFields' => 'title,slug',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'typeicon_classes' => [
            'default' => 'tx-feuerwehren-person'
        ],
    ],
    'columns' => [
        'hidden' => ['config' => ['type' => 'check']],
        'title' => ['label' => 'Titel', 'config' => ['type' => 'input', 'eval' => 'trim,required']],
        'slug' => [
            'label' => 'Slug',
            'config' => [
                'type' => 'slug',
                'generatorOptions' => ['fields' => ['title']],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite'
            ]
        ],
        'fe_user' => [
            'label' => 'FE-User',
            'config' => [
                'type' => 'select', 'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users', 'maxitems' => 1, 'items' => [['-', 0]]
            ]
        ],
        'rolle' => [
            'label' => 'Rolle',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['KBR', 'kbr'],
                    ['KBI', 'kbi'],
                    ['KBM', 'kbm'],
                    ['Fach-KBM', 'fach_kbm'],
                ],
                'default' => '',
            ],
        ],
        'gemeinde' => [
            'label' => 'Gemeinden',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_feuerwehren_domain_model_gemeinde',
                'MM' => 'tx_feuerwehren_person_gemeinde_mm',
                'size' => 10,
                'autoSizeMax' => 30,
            ],
        ],
        'untergeordnet' => [
            'label' => 'Untergeordnete Personen',
            'config' => [
                'type' => 'select', 'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_feuerwehren_domain_model_person',
                'MM' => 'tx_feuerwehren_person_person_mm', 'size' => 10, 'autoSizeMax' => 30,
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, slug, fe_user, rolle, gemeinde, untergeordnet, 
            --div--;Access, hidden']],
];