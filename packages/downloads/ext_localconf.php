<?php

declare(strict_types=1);

defined('TYPO3') or die();

use Schmid\Downloads\Controller\DownloadController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
    'Downloads',
    'Downloads',
    [
        DownloadController::class => 'list',
    ],
    []
);
