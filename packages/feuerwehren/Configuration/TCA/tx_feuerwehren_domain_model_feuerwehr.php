<?php
// TCA Definitions

// === TCA for Feuerwehr ===
return [
    'ctrl' => [
        'title' => 'Feuerwehr',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'hidden' => 'hidden',
        'iconfile' => 'EXT:feuerwehren/Resources/Public/Icons/feuerwehr.svg',
    ],
    'columns' => [
        'name' => ['label' => 'Name', 'config' => ['type' => 'input']],
        'slug' => ['label' => 'Slug', 'config' => ['type' => 'slugd']],
        'strasse' => ['label' => 'Straße + Hausnummer', 'config' => ['type' => 'input']],
        'plz' => ['label' => 'PLZ', 'config' => ['type' => 'input']],
        'ort' => ['label' => 'Ort', 'config' => ['type' => 'input']],
        'latitude' => ['label' => 'Latitude', 'config' => ['type' => 'input']],
        'longitude' => ['label' => 'Longitude', 'config' => ['type' => 'input']],
        'gruendungsdatum' => ['label' => 'Gründungsjahr', 'config' => ['type' => 'input', 'eval' => 'date']],
        'kommandant' => ['label' => 'Kommandant', 'config' => ['type' => 'group', 'internal_type' => 'db', 'allowed' => 'fe_users']],
        'stellv_kommandant' => ['label' => 'Stv. Kommandant', 'config' => ['type' => 'group', 'internal_type' => 'db', 'allowed' => 'fe_users']],
        'jugendwart' => ['label' => 'Jugendwart', 'config' => ['type' => 'group', 'internal_type' => 'db', 'allowed' => 'fe_users']],
        'stellv_jugendwart' => ['label' => 'Stv. Jugendwart', 'config' => ['type' => 'group', 'internal_type' => 'db', 'allowed' => 'fe_users']],
        'kinderwart' => ['label' => 'Kinderwart', 'config' => ['type' => 'group', 'internal_type' => 'db', 'allowed' => 'fe_users']],
        'stellv_kinderwart' => ['label' => 'Stv. Kinderwart', 'config' => ['type' => 'group', 'internal_type' => 'db', 'allowed' => 'fe_users']],
        'fahrzeugkategorien' => [
            'label' => 'Fahrzeuge',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_feuerwehren_domain_model_fahrzeugkategorie',
                'MM' => 'tx_feuerwehren_feuerwehr_fahrzeugkategorie_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'multiple' => 1,
                'renderType' => 'selectMultipleSideBySide'
            ]
        ]
    ],
    'types' => [
        '0' => [
            'showitem' => 'name, slug, strasse, plz, ort, latitude, longitude, gruendungsdatum, fahrzeugkategorien, kommandant, stellv_kommandant, jugendwart, stellv_jugendwart, kinderwart, stellv_kinderwart'
        ]
    ]
];