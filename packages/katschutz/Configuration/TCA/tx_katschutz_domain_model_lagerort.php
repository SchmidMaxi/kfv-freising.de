<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Lagerort',
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
        '0' => ['showitem' => 'title, beschreibung, --div--;core.form.tabs:access, hidden'],
    ],
    'columns' => [
        'title' => [
            'label' => 'Bezeichnung (z.B. Halle A / Regal 3)',
            'config' => ['type' => 'input', 'required' => true, 'max' => 255, 'eval' => 'trim'],
        ],
        'beschreibung' => [
            'label' => 'Beschreibung',
            'config' => ['type' => 'text', 'rows' => 3],
        ],
    ],
];
