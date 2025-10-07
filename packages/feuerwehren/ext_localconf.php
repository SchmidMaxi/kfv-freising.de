<?php
declare(strict_types=1);

defined('TYPO3') or die();

/**
 * Cache für Rate-Limit (60s)
 * Wir registrieren den Cache hier direkt, um Lade-Reihenfolge-Probleme zu umgehen.
 */
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['feuerwehren_rate'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['feuerwehren_rate'] = [
        'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
        'options' => [
            'defaultLifetime' => 60,
        ],
    ];
}

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
    [
        PersonController::class => 'list,show'
    ],
    []
);

// Karte
ExtensionUtility::configurePlugin(
    'Feuerwehren',
    'Karte',
    [
        FeuerwehrController::class => 'list,show',
    ],
    []
);


// Jubiläen
ExtensionUtility::configurePlugin(
    'Feuerwehren',
    'Jubilaeen',
    [
        JubilaeumController::class => 'list,show'
    ],
    []
);
