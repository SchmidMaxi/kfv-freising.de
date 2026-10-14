<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Großschadenslage',
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
        'default_sortby' => 'beginn DESC',
        'searchFields' => 'title',
    ],
    'types' => [
        '0' => ['showitem' => 'title, aktiv, beginn, ende, bemerkung, --div--;core.form.tabs:access, hidden'],
    ],
    'columns' => [
        'title' => [
            'label' => 'Bezeichnung',
            'config' => ['type' => 'input', 'required' => true, 'max' => 255, 'eval' => 'trim'],
        ],
        'aktiv' => [
            'label' => 'Aktiv (Freigabe entfällt)',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'default' => 0],
        ],
        'beginn' => [
            'label' => 'Beginn',
            'config' => ['type' => 'datetime', 'default' => 0],
        ],
        'ende' => [
            'label' => 'Ende',
            'config' => ['type' => 'datetime', 'default' => 0],
        ],
        'bemerkung' => [
            'label' => 'Bemerkung',
            'config' => ['type' => 'text', 'rows' => 3],
        ],
    ],
];
