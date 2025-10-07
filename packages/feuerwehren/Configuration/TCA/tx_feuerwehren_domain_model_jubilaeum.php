<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_jubilaeum',
        'label' => 'titel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'titel,beschreibung',
        'typeicon_classes' => [
            'default' => 'tx-feuerwehren-jubilaeum'
        ],
        'hideTable' => true,
    ],
    'types' => [
        '1' => ['showitem' => 'titel, datum, beschreibung, --div--;Access, hidden',],
    ],
    'columns' => [
        'titel' => [
            'label' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_jubilaeum.titel',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required',
                'default' => 0
            ]
        ],
        'datum' => [
            'label' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_jubilaeum.datum',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'dbType' => 'date',
            ]
        ],
        'beschreibung' => [
            'label' => 'LLL:EXT:feuerwehren/Resources/Private/Language/locallang_db.xlf:tx_feuerwehren_domain_model_jubilaeum.beschreibung',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'feuerwehr' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];