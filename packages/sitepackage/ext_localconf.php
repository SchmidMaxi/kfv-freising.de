<?php

defined('TYPO3') or die('Access denied.');

// Add default RTE configuration
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['sitepackage'] = 'EXT:sitepackage/Configuration/RTE/Default.yaml';

// Termine (Jubiläen + optional Calendarize-Termine)
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sitepackage',
    'Termine',
    [
        \Schmid\Sitepackage\Controller\TermineController::class => 'list',
    ],
    []
);
