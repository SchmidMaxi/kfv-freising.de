<?php
declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;


ExtensionUtility::registerPlugin(
    'feuerwehren',
    'Karte',
    'Karte',
    'EXT:feuerwehren/Resources/Public/Icons/extension.svg'
);
// Fügt die FlexForm-Konfiguration zum Plugin hinzu
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['feuerwehren_karte'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'feuerwehren_karte',
    'FILE:EXT:feuerwehren/Configuration/FlexForms/karte.xml'
);



ExtensionUtility::registerPlugin(
    'feuerwehren',
    'Organigramm',
    'Organigramm',
    'EXT:feuerwehren/Resources/Public/Icons/extension.svg'
);
ExtensionUtility::registerPlugin(
    'feuerwehren',
    'Jubilaeen',
    'Jubiläen',
    'EXT:feuerwehren/Resources/Public/Icons/extension.svg'
);
// Fügt die FlexForm-Konfiguration zum Plugin hinzu
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['feuerwehren_jubilaeen'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'feuerwehren_jubilaeen',
    'FILE:EXT:feuerwehren/Configuration/FlexForms/jubilaeum.xml'
);