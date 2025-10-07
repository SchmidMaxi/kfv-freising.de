<?php
return [
    'ctrl' => [
        'title' => 'Jubiläum',
        'label' => 'titel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'typeicon_classes' => [
            'default' => 'tx-feuerwehren-jubilaeum'
        ],
    ],
    'columns' => [
        'hidden' => [
            'config' => [
                'type' => 'check'
            ]
        ],
        'feuerwehr' => [
            'label' => 'Feuerwehr',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_feuerwehren_domain_model_feuerwehr',
                'maxitems' => 1
            ]
        ],
        'date' => [
            'label' => 'Datum',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'dbType' => 'date',
                'default' => null,
                'nullable' => true,
            ]
        ],
        'titel' => [
            'label' => 'Titel',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required'
            ]
        ],
        'beschreibung' => [
            'label' => 'Beschreibung',
            'config' => [
                'type' => 'text',
                'rows' => 6
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
            feuerwehr, date, titel, beschreibung, 
            --div--;Access, hidden'
        ]
    ],
];