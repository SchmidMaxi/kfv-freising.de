<?php

declare(strict_types=1);

$feUserSelect = [
    'type' => 'select',
    'renderType' => 'selectSingle',
    'foreign_table' => 'fe_users',
    'foreign_table_where' => 'ORDER BY fe_users.username',
    'items' => [['label' => '–', 'value' => 0]],
    'default' => 0,
];

return [
    'ctrl' => [
        'title' => 'KatS: Bestellung',
        'label' => 'nummer',
        'label_alt' => 'anlass',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'default_sortby' => 'crdate DESC',
        'searchFields' => 'nummer,anlass,einsatzabschnitt,notiz',
        'typeicon_classes' => [
            'default' => 'tx-katschutz-record',
        ],
    ],
    'types' => [
        '0' => ['showitem' => '
            nummer, status, feuerwehr, bereichsfremd, besteller,
            --palette--;Zeitraum;zeitraum,
            anlass, einsatzabschnitt, notiz, lage,
            --div--;Positionen, positionen,
            --div--;Verwaltung, verwalter_notiz, freigegeben_von, freigegeben_am'],
    ],
    'palettes' => [
        'zeitraum' => ['showitem' => 'von, bis'],
    ],
    'columns' => [
        // ctrl-Feld, als Spalte deklariert, damit Extbase es auf $crdate mappt
        'crdate' => [
            'label' => 'Erstellt',
            'config' => ['type' => 'datetime', 'readOnly' => true],
        ],
        'nummer' => [
            'label' => 'Bestellnummer',
            'config' => ['type' => 'input', 'max' => 20, 'readOnly' => true],
        ],
        'status' => [
            'label' => 'Status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'neu', 'value' => 'neu'],
                    ['label' => 'freigegeben', 'value' => 'freigegeben'],
                    ['label' => 'ausgegeben', 'value' => 'ausgegeben'],
                    ['label' => 'abgeschlossen', 'value' => 'abgeschlossen'],
                    ['label' => 'abgelehnt', 'value' => 'abgelehnt'],
                    ['label' => 'storniert', 'value' => 'storniert'],
                ],
                'default' => 'neu',
            ],
        ],
        'feuerwehr' => [
            'label' => 'Anfordernde Feuerwehr',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_feuerwehren_domain_model_feuerwehr',
                'foreign_table_where' => 'ORDER BY tx_feuerwehren_domain_model_feuerwehr.name',
                'required' => true,
                'minitems' => 1,
                'default' => 0,
            ],
        ],
        'bereichsfremd' => [
            'label' => 'Feuerwehr außerhalb des eigenen Bereichs',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'default' => 0, 'readOnly' => true],
        ],
        'besteller' => [
            'label' => 'Besteller',
            'config' => $feUserSelect,
        ],
        'von' => [
            'label' => 'Benötigt ab',
            'config' => ['type' => 'datetime', 'format' => 'date', 'dbType' => 'date', 'nullable' => true],
        ],
        'bis' => [
            'label' => 'Rückgabe bis',
            'config' => ['type' => 'datetime', 'format' => 'date', 'dbType' => 'date', 'nullable' => true],
        ],
        'anlass' => [
            'label' => 'Anlass / Einsatznummer',
            'config' => ['type' => 'input', 'required' => true, 'max' => 255, 'eval' => 'trim'],
        ],
        'einsatzabschnitt' => [
            'label' => 'Einsatzabschnitt / Begründung',
            'config' => ['type' => 'input', 'max' => 255, 'eval' => 'trim'],
        ],
        'notiz' => [
            'label' => 'Notiz (Lieferung, Ansprechpartner …)',
            'config' => ['type' => 'text', 'rows' => 3],
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
        'positionen' => [
            'label' => 'Positionen',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_katschutz_domain_model_position',
                'foreign_field' => 'bestellung',
                'foreign_sortby' => 'sorting',
                'appearance' => ['useSortable' => true],
            ],
        ],
        'verwalter_notiz' => [
            'label' => 'Notiz Verwalter (z.B. Ablehnungsgrund)',
            'config' => ['type' => 'text', 'rows' => 3],
        ],
        'freigegeben_von' => [
            'label' => 'Freigegeben von',
            'config' => $feUserSelect + ['readOnly' => true],
        ],
        'freigegeben_am' => [
            'label' => 'Freigegeben am',
            'config' => ['type' => 'datetime', 'default' => 0, 'readOnly' => true],
        ],
    ],
];
