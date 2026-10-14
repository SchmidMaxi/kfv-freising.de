<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Bestellposition',
        'label' => 'artikel',
        'label_alt' => 'menge',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'hideTable' => true,
        'typeicon_classes' => [
            'default' => 'tx-katschutz-record',
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'artikel, menge, modul'],
    ],
    'columns' => [
        'bestellung' => [
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
        'modul' => [
            'label' => 'Aus Modul',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_modul',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
    ],
];
