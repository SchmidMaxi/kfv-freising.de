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
