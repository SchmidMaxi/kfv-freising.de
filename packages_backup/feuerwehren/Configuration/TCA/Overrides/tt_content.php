<?php
declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Wichtig: registerPlugin mit dem Extension-Key "feuerwehren"
ExtensionUtility::registerPlugin('feuerwehren', 'Karte', 'Karte', 'EXT:feuerwehren/Resources/Public/Icons/extension.svg');
ExtensionUtility::registerPlugin('feuerwehren', 'Organigramm', 'Organigramm', 'EXT:feuerwehren/Resources/Public/Icons/extension.svg');
ExtensionUtility::registerPlugin('feuerwehren', 'Jubilaeen', 'Jubiläen', 'EXT:feuerwehren/Resources/Public/Icons/extension.svg');
