<?php

declare(strict_types=1);

$feuerwehrSelect = [
    'type' => 'select',
    'renderType' => 'selectSingle',
    'foreign_table' => 'tx_feuerwehren_domain_model_feuerwehr',
    'foreign_table_where' => 'ORDER BY tx_feuerwehren_domain_model_feuerwehr.name',
    'items' => [['label' => 'KatS-Halle', 'value' => 0]],
    'default' => 0,
];

// Buchungsjournal: Datensätze werden ausschließlich vom BuchungService angelegt und
// nie geändert (Audit-Trail). Daher im Backend schreibgeschützt.
return [
    'ctrl' => [
        'title' => 'KatS: Buchung',
        'label' => 'typ',
        'label_alt' => 'artikel,menge',
        'label_alt_force' => true,
        'crdate' => 'crdate',
        'default_sortby' => 'uid DESC',
        'readOnly' => true,
        'searchFields' => 'bemerkung',
        'typeicon_classes' => [
            'default' => 'tx-katschutz-record',
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'typ, artikel, einzelstueck, menge, feuerwehr, ziel_feuerwehr, bestellung, lage, fe_user, bemerkung'],
    ],
    'columns' => [
        // ctrl-Feld, als Spalte deklariert, damit Extbase es auf $crdate mappt
        'crdate' => [
            'label' => 'Erstellt',
            'config' => ['type' => 'datetime', 'readOnly' => true],
        ],
        'typ' => [
            'label' => 'Buchungsart',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Zugang', 'value' => 'zugang'],
                    ['label' => 'Inventur-Korrektur', 'value' => 'korrektur'],
                    ['label' => 'Aussonderung', 'value' => 'aussonderung'],
                    ['label' => 'Ausgabe', 'value' => 'ausgabe'],
                    ['label' => 'Rücknahme', 'value' => 'ruecknahme'],
                    ['label' => 'Umbuchung', 'value' => 'umbuchung'],
                    ['label' => 'Verlust', 'value' => 'verlust'],
                ],
                'default' => 'zugang',
            ],
        ],
        'artikel' => [
            'label' => 'Artikel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_artikel',
                'default' => 0,
            ],
        ],
        'einzelstueck' => [
            'label' => 'Einzelstück',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_einzelstueck',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
        'menge' => [
            'label' => 'Menge',
            'config' => ['type' => 'number', 'default' => 0],
        ],
        'feuerwehr' => [
            'label' => 'Feuerwehr',
            'config' => $feuerwehrSelect,
        ],
        'ziel_feuerwehr' => [
            'label' => 'Ziel-Feuerwehr (Umbuchung)',
            'config' => $feuerwehrSelect,
        ],
        'bestellung' => [
            'label' => 'Bestellung',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_bestellung',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
        'lage' => [
            'label' => 'Großschadenslage',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_katschutz_domain_model_lage',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
        'fe_user' => [
            'label' => 'Gebucht von',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'items' => [['label' => '–', 'value' => 0]],
                'default' => 0,
            ],
        ],
        'bemerkung' => [
            'label' => 'Bemerkung',
            'config' => ['type' => 'text', 'rows' => 2],
        ],
    ],
];
