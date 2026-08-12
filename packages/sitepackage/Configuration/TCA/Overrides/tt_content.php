<?php
defined('TYPO3') or die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'badge' => [
        'label' => 'Badge',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'max' => 255,
            'placeholder' => 'z.B. Neuigkeiten',
        ],
    ],
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'header',
    'badge',
    'before:header'
);

// Termine (Jubiläen + optional Calendarize-Termine)
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sitepackage',
    'Termine',
    'Termine (Jubiläen + Calendarize)',
    'EXT:sitepackage/Resources/Public/Icons/Extension.svg'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['sitepackage_termine'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'sitepackage_termine',
    'FILE:EXT:sitepackage/Configuration/FlexForms/Termine.xml'
);
