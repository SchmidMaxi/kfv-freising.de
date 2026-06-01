<?php

declare(strict_types=1);

use Schmid\IcsImporter\Task\ImportWithCategoryAdditionalFieldProvider;
use Schmid\IcsImporter\Task\ImportWithCategoryTask;

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][ImportWithCategoryTask::class] = [
    'extension' => 'icsimporter',
    'title' => 'LLL:EXT:icsimporter/Resources/Private/Language/locallang.xlf:task.title',
    'description' => 'LLL:EXT:icsimporter/Resources/Private/Language/locallang.xlf:task.description',
    'additionalFields' => ImportWithCategoryAdditionalFieldProvider::class,
];
