<?php

defined('TYPO3') || die();
call_user_func(function () {
    /**
     * Temporary variables
     */
    $extensionKey = 'kfv_template';

    /**
     * Default TypoScript for ForBadwelt
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'KFV Template'
    );
});
