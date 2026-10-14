<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Prüfung',
        'label' => 'datum',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'tx-katschutz-record',
        ],
        'label_alt' => 'ergebnis,pruefer',
        'label_alt_force' => true,
        'default_sortby' => 'datum DESC',
        'hideTable' => true,
    ],
    'types' => [
        '0' => ['showitem' => 'datum, ergebnis, pruefer, bemerkung'],
    ],
    'columns' => [
        'einzelstueck' => [
            'config' => ['type' => 'passthrough'],
        ],
        'datum' => [
            'label' => 'Datum',
            'config' => ['type' => 'datetime', 'format' => 'date', 'dbType' => 'date', 'required' => true],
        ],
        'ergebnis' => [
            'label' => 'Ergebnis',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'bestanden', 'value' => 'bestanden'],
                    ['label' => 'Mangel festgestellt', 'value' => 'mangel'],
                ],
                'default' => 'bestanden',
            ],
        ],
        'pruefer' => [
            'label' => 'Prüfer',
            'config' => ['type' => 'input', 'max' => 255, 'eval' => 'trim'],
        ],
        'bemerkung' => [
            'label' => 'Bemerkung',
            'config' => ['type' => 'text', 'rows' => 3],
        ],
    ],
];
