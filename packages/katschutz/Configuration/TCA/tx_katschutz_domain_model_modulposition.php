<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Modul-Position',
        'label' => 'artikel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'tx-katschutz-record',
        ],
        'label_alt' => 'menge',
        'label_alt_force' => true,
        'sortby' => 'sorting',
        'hideTable' => true,
    ],
    'types' => [
        '0' => ['showitem' => 'artikel, menge'],
    ],
    'columns' => [
        'modul' => [
            'config' => ['type' => 'passthrough'],
        ],
        'artikel' => [
            'label' => 'Artikel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_artikel',
                'foreign_table_where' => 'ORDER BY tx_katschutz_domain_model_artikel.title',
                'default' => 0,
            ],
        ],
        'menge' => [
            'label' => 'Menge',
            'config' => ['type' => 'number', 'default' => 1, 'range' => ['lower' => 1]],
        ],
    ],
];
