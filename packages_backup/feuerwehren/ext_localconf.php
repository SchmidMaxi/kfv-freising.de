<?php
declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Schmid\Feuerwehren\Controller\{
    FeuerwehrController,
    PersonController,
    JubilaeumController,
    TileController
};

/**
 * WICHTIG:
 * 1) Erster Parameter = ExtensionName OHNE Vendor -> 'Feuerwehren' (UpperCamelCase)
 * 2) PluginName exakt wie in tt_content-Plugin -> 'Karte', 'Organigramm', 'Jubilaeen'
 * 3) API- und Tile-Actions beim Karte-Plugin registrieren (und non-cacheable)
 */

// Organigramm
ExtensionUtility::configurePlugin(
    'Feuerwehren',
    'Organigramm',
    [PersonController::class => 'list,show'],
    []
);

// Karte
ExtensionUtility::configurePlugin(
    'Feuerwehren',
    'Karte',
    [
        FeuerwehrController::class => 'list,show',
        TileController::class      => 'tileByQuery',
    ],
    [
        TileController::class      => 'tileByQuery',
    ]
);


// Jubiläen
ExtensionUtility::configurePlugin(
    'Feuerwehren',
    'Jubilaeen',
    [JubilaeumController::class => 'list,show'],
    []
);
