<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Modul / Set',
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
        '0' => ['showitem' => 'title, beschreibung, positionen, --div--;core.form.tabs:access, hidden'],
    ],
    'columns' => [
        'title' => [
            'label' => 'Bezeichnung',
            'config' => ['type' => 'input', 'required' => true, 'max' => 255, 'eval' => 'trim'],
        ],
        'beschreibung' => [
            'label' => 'Beschreibung',
            'config' => ['type' => 'text', 'rows' => 3],
        ],
        'positionen' => [
            'label' => 'Inhalt',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_katschutz_domain_model_modulposition',
                'foreign_field' => 'modul',
                'foreign_sortby' => 'sorting',
                'appearance' => ['useSortable' => true],
            ],
        ],
    ],
];
