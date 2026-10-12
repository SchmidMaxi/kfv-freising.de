<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Own dedicated CTypes (not the classic CType=list + list_type convention): TYPO3 v14's
// FlexForm resolution keys strictly off CType (FlexFormTools::getDataStructureIdentifierFromTcaSchema
// resolves via the record's ctrl.type field, i.e. CType, not list_type), so a shared CType=list can
// no longer carry a per-plugin data structure — addPiFlexFormValue()'s old 2-arg call is a silent
// no-op in v14 (it early-returns unless a 3rd $CTypeToMatch arg is given), meaning Karte's and
// Jubilaeen's FlexForms were never actually reachable in the backend after the v13->14 upgrade.
// Passing $flexForm directly to registerPlugin() self-wires both the "ds" and the showitem
// "Plugin" tab. Existing CType=list rows are migrated by Classes/Updates/MigratePluginCTypeUpdate.
ExtensionUtility::registerPlugin(
    'feuerwehren',
    'Karte',
    'Karte',
    'ext-feuerwehren-plugin-karte',
    'plugins',
    '',
    'FILE:EXT:feuerwehren/Configuration/FlexForms/karte.xml'
);

ExtensionUtility::registerPlugin(
    'feuerwehren',
    'Organigramm',
    'Organigramm',
    'ext-feuerwehren-plugin-organigramm'
);

ExtensionUtility::registerPlugin(
    'feuerwehren',
    'Jubilaeen',
    'Jubiläen',
    'ext-feuerwehren-plugin-jubilaeen',
    'plugins',
    '',
    'FILE:EXT:feuerwehren/Configuration/FlexForms/jubilaeum.xml'
);
