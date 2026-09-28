<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Own dedicated CType ("downloads_downloads"), not the classic CType=list + list_type
// convention: TYPO3 v14's FlexForm resolution keys strictly off CType (see
// FlexFormTools::getDataStructureIdentifierFromTcaSchema), so a shared CType=list can no
// longer carry a per-plugin data structure. Passing $flexForm here self-wires both the
// "ds" and the showitem "Plugin" tab — no manual subtypes_addlist/addPiFlexFormValue needed.
ExtensionUtility::registerPlugin(
    'downloads',
    'Downloads',
    'Downloads',
    'ext-downloads-plugin-downloads',
    'plugins',
    '',
    'FILE:EXT:downloads/Configuration/FlexForms/downloads.xml'
);
