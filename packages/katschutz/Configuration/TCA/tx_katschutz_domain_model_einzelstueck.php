<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Einzelstück',
        'label' => 'inventarnummer',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'tx-katschutz-record',
        ],
        'label_alt' => 'seriennummer',
        'default_sortby' => 'inventarnummer',
        'searchFields' => 'inventarnummer,seriennummer,bemerkung',
    ],
    'types' => [
        '0' => ['showitem' => 'artikel, inventarnummer, seriennummer, lagerort, zustand, naechste_pruefung, bemerkung, pruefungen, --div--;core.form.tabs:access, hidden'],
    ],
    'columns' => [
        'artikel' => [
            'label' => 'Artikel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_artikel',
                'foreign_table_where' => 'AND tx_katschutz_domain_model_artikel.typ = \'einzeln\' ORDER BY tx_katschutz_domain_model_artikel.title',
                'default' => 0,
            ],
        ],
        'inventarnummer' => [
            'label' => 'Inventarnummer',
            'config' => ['type' => 'input', 'required' => true, 'max' => 50, 'eval' => 'trim,uniqueInPid'],
        ],
        'seriennummer' => [
            'label' => 'Seriennummer',
            'config' => ['type' => 'input', 'max' => 100, 'eval' => 'trim'],
        ],
        'lagerort' => [
            'label' => 'Lagerort (leer = Lagerort des Artikels)',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_lagerort',
                'foreign_table_where' => 'ORDER BY tx_katschutz_domain_model_lagerort.title',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
        'zustand' => [
            'label' => 'Zustand',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'einsatzbereit', 'value' => 'ok'],
                    ['label' => 'Mangel', 'value' => 'mangel'],
                    ['label' => 'defekt / gesperrt', 'value' => 'defekt'],
                ],
                'default' => 'ok',
            ],
        ],
        'naechste_pruefung' => [
            'label' => 'Nächste Prüfung',
            'config' => ['type' => 'datetime', 'format' => 'date', 'dbType' => 'date', 'nullable' => true],
        ],
        'bemerkung' => [
            'label' => 'Bemerkung',
            'config' => ['type' => 'text', 'rows' => 3],
        ],
        'pruefungen' => [
            'label' => 'Prüfungen',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_katschutz_domain_model_pruefung',
                'foreign_field' => 'einzelstueck',
                'foreign_default_sortby' => 'datum DESC',
                'appearance' => ['collapseAll' => true, 'useSortable' => false],
            ],
        ],
    ],
];
