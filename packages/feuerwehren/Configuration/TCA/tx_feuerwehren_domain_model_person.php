<?php
// TCA Definitions

// === TCA for Person ===
return [
    'ctrl' => [
        'title' => 'Person',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'hidden' => 'hidden',
        'iconfile' => 'EXT:feuerwehren/Resources/Public/Icons/person.svg',
    ],
    'columns' => [
        'title' => ['label' => 'Titel', 'config' => ['type' => 'input']],
        'slug' => ['label' => 'Slug', 'config' => ['type' => 'input']],
        'fe_user' => ['label' => 'FE User', 'config' => ['type' => 'group', 'internal_type' => 'db', 'allowed' => 'fe_users']],
        'rolle' => ['label' => 'Rolle', 'config' => ['type' => 'select', 'foreign_table' => 'tx_feuerwehren_domain_model_rolle']],
        'gemeinde' => ['label' => 'Gemeinde', 'config' => ['type' => 'select', 'foreign_table' => 'tx_feuerwehren_domain_model_gemeinde']],
        'untergeordnet' => [
            'label' => 'Untergeordnete Personen',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_feuerwehren_domain_model_person',
                'MM' => 'tx_feuerwehren_person_person_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'multiple' => 1,
                'renderType' => 'selectMultipleSideBySide'
            ]
        ]
    ],
    'types' => ['0' => ['showitem' => 'title, slug, fe_user, rolle, gemeinde, untergeordnet']]
];