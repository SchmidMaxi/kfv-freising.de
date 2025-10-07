<?php
defined('TYPO3') || die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_area',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
        ],
        'searchFields' => 'title,type',
        'type' => 'type',
        'typeicon_classes' => [
            'default'   => 'tx-feuerwehren-area',
            'kbr'       => 'tx-feuerwehren-area-kbr',
            'kbi'       => 'tx-feuerwehren-area-kbi',
            'kbm'       => 'tx-feuerwehren-area-kbm',
            'fach-kbm'  => 'tx-feuerwehren-area-fachkbm',
        ],
        'iconfile' => 'EXT:feuerwehren/Resources/Public/Icons/tx_feuerwehren_domain_model_area.svg',
    ],

    'types' => [
        // KBR – ohne Parent
        'kbr' => [
            'showitem' => '
                --palette--;;core,
                title, type, person,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;access
            ',
        ],
        // KBI – Parent = KBR
        'kbi' => [
            'showitem' => '
                --palette--;;core,
                title, type, parent, person,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;access
            ',
        ],
        // KBM – Parent = KBI, plus (optionale) Gemeinde-Zuordnung (IRRE)
        'kbm' => [
            'showitem' => '
                --palette--;;core,
                title, type, parent, person, gemeinden,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;access
            ',
        ],
        // Fach-KBM – Parent = KBI (optional)
        'fach-kbm' => [
            'showitem' => '
                --palette--;;core,
                title, type, parent, person,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;access
            ',
        ],
        // Fallback
        '0' => [
            'showitem' => '
                --palette--;;core,
                title, type, parent, person,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;access
            ',
        ],
    ],

    'palettes' => [
        'core' => [
            'showitem' => 'hidden,--linebreak--',
        ],
        'access' => [
            'showitem' => 'starttime, endtime',
        ],
    ],

    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [['', '']],
            ],
        ],
        'starttime' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type'       => 'datetime',
                'renderType' => 'inputDateTime',
                'eval'       => 'datetime,int',
                'default'    => 0,
            ],
        ],
        'endtime' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type'       => 'datetime',
                'renderType' => 'inputDateTime',
                'eval'       => 'datetime,int',
                'default'    => 0,
            ],
        ],

        'title' => [
            'label' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_area.title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim,required',
            ],
        ],

        // kbr | kbi | kbm | fach-kbm
        'type' => [
            'label' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_area.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['KBR', 'kbr'],
                    ['KBI', 'kbi'],
                    ['KBM', 'kbm'],
                    ['Fach-KBM', 'fach-kbm'],
                ],
                'eval' => 'required',
                'default' => 'kbm',
                'onChange' => 'reload', // wichtig, damit displayCond greift
            ],
        ],

        // Selbst-Referenz auf übergeordnete Area
        'parent' => [
            'label' => 'Parent area',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_feuerwehren_domain_model_area',
                // Nur Bedingung(en) hier lassen – KEIN ORDER BY!
                'foreign_table_where' => 'AND tx_feuerwehren_domain_model_area.type IN (\'kbi\', \'kbr\')',
                // Sortierung sauber getrennt:
                'foreign_table_orderby' => 'title',
            ],
        ],


        // optionale Verknüpfung zur Person (Inhaber/Ansprechpartner)
        'person' => [
            'label' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_area.person',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.no_value', 0],
                ],
                'foreign_table' => 'tx_feuerwehren_domain_model_person',
                'foreign_table_where' =>
                    ' AND {#tx_feuerwehren_domain_model_person}.{#pid}=###CURRENT_PID###' .
                    ' AND {#tx_feuerwehren_domain_model_person}.{#deleted}=0' .
                    ' AND {#tx_feuerwehren_domain_model_person}.{#hidden}=0',
                'default' => 0,
            ],
        ],

        // Nur zur Übersicht/Zuordnung bei KBM: zugeordnete Gemeinden (inverse Relation)
        // Funktioniert, wenn an Gemeinde das Feld "kbm_area" existiert (foreign_field).
        'gemeinden' => [
            'label' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_area.gemeinden',
            'displayCond' => 'FIELD:type:=:kbm',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_feuerwehren_domain_model_gemeinde',
                'foreign_field' => 'kbm_area',
                'appearance' => [
                    'collapseAll' => 1,
                    'useSortable' => 0,
                    'newRecordLinkAddTitle' => 1,
                    'levelLinksPosition' => 'both',
                    'showPossibleLocalizationRecords' => 0,
                    'showRemovedLocalizationRecords' => 0,
                ],
            ],
        ],
    ],
];
