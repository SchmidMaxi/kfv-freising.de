<?php
declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Schmid\Feuerwehren\Controller\{FeuerwehrController,PersonController,JubilaeumController};

ExtensionUtility::configurePlugin(
    'Feuerwehren',                                // ExtensionName (UpperCamelCase)
    'Karte',                                      // PluginName
    [FeuerwehrController::class => 'list,show'],
    []
);

ExtensionUtility::configurePlugin(
    'Feuerwehren',
    'Organigramm',
    [PersonController::class => 'list,show'],
    []
);

ExtensionUtility::configurePlugin(
    'Feuerwehren',
    'Jubilaeen',
    [JubilaeumController::class => 'list,show'],
    []
);
