<?php
defined('TYPO3') or die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'badge' => [
        'label' => 'Badge',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'max' => 255,
            'placeholder' => 'z.B. Neuigkeiten',
        ],
    ],
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'header',
    'badge',
    'before:header'
);
