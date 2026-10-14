<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Eigene CTypes (vgl. EXT:feuerwehren), kein CType=list.
ExtensionUtility::registerPlugin(
    'katschutz',
    'Bestellung',
    'KatS-Inventar: Katalog & Bestellung (Inspektion)',
    'ext-katschutz-plugin-bestellung',
    'plugins'
);

ExtensionUtility::registerPlugin(
    'katschutz',
    'Verwaltung',
    'KatS-Inventar: Verwaltung & Dashboard (Verwalter)',
    'ext-katschutz-plugin-verwaltung',
    'plugins'
);

// Datensatzsammlung (Ordner mit Inventar & Bestellungen) direkt am Plugin wählbar
foreach (['katschutz_bestellung', 'katschutz_verwaltung'] as $cType) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;core.form.tabs:plugin, pages',
        $cType,
        'after:palette:headers'
    );
}
