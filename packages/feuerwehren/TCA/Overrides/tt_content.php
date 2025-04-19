<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die;

return [
    'types' => [
        'list' => [
            'subtypes_addlist' => [
                'feuerwehren_organigramm' => 'pi_flexform',
                'feuerwehren_karte' => 'pi_flexform',
                'feuerwehren_jubilaeen' => 'pi_flexform',
            ],
            'subtypes_excludelist' => [
                'feuerwehren_organigramm' => 'layout,select_key',
                'feuerwehren_karte' => 'layout,select_key',
                'feuerwehren_jubilaeen' => 'layout,select_key',
            ],
        ],
    ]
];