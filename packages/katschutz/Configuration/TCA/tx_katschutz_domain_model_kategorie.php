<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'KatS: Kategorie',
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
        'sortby' => 'sorting',
        'searchFields' => 'title',
    ],
    'types' => [
        '0' => ['showitem' => 'title, --div--;core.form.tabs:access, hidden'],
    ],
    'columns' => [
        'title' => [
            'label' => 'Titel',
            'config' => ['type' => 'input', 'required' => true, 'max' => 255, 'eval' => 'trim'],
        ],
    ],
];
