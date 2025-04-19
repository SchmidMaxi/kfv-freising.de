<?php

use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

use Schmid\Feuerwehren\Controller\PersonController;
use Schmid\Feuerwehren\Controller\FeuerwehrController;
use Schmid\Feuerwehren\Controller\JubilaeumController;

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Schmid.Feuerwehren',
    'Organigramm',
    [PersonController::class => 'list, show'],
    [PersonController::class => 'list, show']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Schmid.Feuerwehren',
    'Karte',
    [FeuerwehrController::class => 'list, show'],
    [FeuerwehrController::class => 'list, show']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Schmid.Feuerwehren',
    'Jubilaeen',
    [JubilaeumController::class => 'list, show'],
    [JubilaeumController::class => 'list, show']
);