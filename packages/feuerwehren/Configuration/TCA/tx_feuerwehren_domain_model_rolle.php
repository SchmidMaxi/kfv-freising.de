<?php
// TCA Definitions

// === TCA for Rolle ===
return [
    'ctrl' => [
        'title' => 'Rolle',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'hidden' => 'hidden',
        'iconfile' => 'EXT:feuerwehren/Resources/Public/Icons/rolle.svg',
    ],
    'columns' => [
        'title' => [
            'label' => 'Titel',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ]
        ]
    ],
    'types' => ['0' => ['showitem' => 'title']]
];