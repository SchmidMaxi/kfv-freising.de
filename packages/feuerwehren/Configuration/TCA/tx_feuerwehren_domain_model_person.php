<?php
return [
    'ctrl' => [
        'title' => 'Person',
        'label' => 'title',
        'searchFields' => 'title,slug',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'typeicon_classes' => [
            'default' => 'tx-feuerwehren-person'
        ],
    ],
    'columns' => [
        'hidden' => ['config' => ['type' => 'check']],
        'title' => ['label' => 'Titel', 'config' => ['type' => 'input', 'eval' => 'trim,required']],
        'slug' => [
            'label' => 'Slug',
            'config' => [
                'type' => 'slug',
                'generatorOptions' => ['fields' => ['title']],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite'
            ]
        ],
        'fe_user' => [
            'label' => 'FE-User',
            'config' => [
                'type' => 'select', 'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users', 'maxitems' => 1, 'items' => [['-', 0]]
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, slug, fe_user, 
            --div--;Access, hidden']],
];