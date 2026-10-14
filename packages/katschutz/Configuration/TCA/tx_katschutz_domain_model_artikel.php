<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Artikel',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'tx-katschutz-record',
        ],
        'default_sortby' => 'title',
        'searchFields' => 'title,beschreibung',
    ],
    'types' => [
        '0' => ['showitem' => '
            title, kategorie, beschreibung, bilder,
            --div--;Bestand, typ, verbrauch, einheit, mindestbestand, lagerort, lagerfach,
            --div--;Prüfung, pruefintervall,
            --div--;Einzelstücke, einzelstuecke,
            --div--;core.form.tabs:access, hidden'],
    ],
    'columns' => [
        'title' => [
            'label' => 'Bezeichnung',
            'config' => ['type' => 'input', 'required' => true, 'max' => 255, 'eval' => 'trim'],
        ],
        'kategorie' => [
            'label' => 'Kategorie',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_kategorie',
                'foreign_table_where' => 'ORDER BY tx_katschutz_domain_model_kategorie.sorting',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
        'beschreibung' => [
            'label' => 'Beschreibung',
            'config' => ['type' => 'text', 'rows' => 5],
        ],
        'bilder' => [
            'label' => 'Bilder',
            'config' => ['type' => 'file', 'allowed' => 'common-image-types', 'maxitems' => 5],
        ],
        'typ' => [
            'label' => 'Erfassung',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Mengenartikel (nur Anzahl)', 'value' => 'menge'],
                    ['label' => 'Einzelstücke (Inventarnummer)', 'value' => 'einzeln'],
                ],
                'default' => 'menge',
            ],
        ],
        'verbrauch' => [
            'label' => 'Verbrauchsmaterial (kommt nicht zurück)',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'default' => 0],
        ],
        'einheit' => [
            'label' => 'Einheit',
            'config' => ['type' => 'input', 'max' => 30, 'default' => 'Stk.', 'eval' => 'trim'],
        ],
        'mindestbestand' => [
            'label' => 'Mindestbestand',
            'config' => ['type' => 'number', 'default' => 0, 'range' => ['lower' => 0]],
        ],
        'lagerort' => [
            'label' => 'Lagerort',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_lagerort',
                'foreign_table_where' => 'ORDER BY tx_katschutz_domain_model_lagerort.title',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
        'lagerfach' => [
            'label' => 'Fach / Position',
            'config' => ['type' => 'input', 'max' => 100, 'eval' => 'trim'],
        ],
        'pruefintervall' => [
            'label' => 'Prüfintervall in Monaten (0 = keine Prüfpflicht)',
            'config' => ['type' => 'number', 'default' => 0, 'range' => ['lower' => 0]],
        ],
        'einzelstuecke' => [
            'label' => 'Einzelstücke',
            'displayCond' => 'FIELD:typ:=:einzeln',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_katschutz_domain_model_einzelstueck',
                'foreign_field' => 'artikel',
                'appearance' => ['collapseAll' => true, 'useSortable' => false],
            ],
        ],
    ],
];
