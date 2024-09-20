<?php
/**
 * Compiled ext_tables.php cache file
 */

/**
 * Extension: setup
 * File: /var/www/html/vendor/typo3/cms-setup/ext_tables.php
 */

namespace {




use TYPO3\CMS\Setup\Controller\SetupModuleController;

defined('TYPO3') or die();

$GLOBALS['TYPO3_USER_SETTINGS'] = [
    'columns' => [
        'realName' => [
            'type' => 'text',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:beUser_realName',
            'table' => 'be_users',
            'max' => 80,
        ],
        'email' => [
            'type' => 'email',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:beUser_email',
            'table' => 'be_users',
            'max' => 255,
        ],
        'emailMeAtLogin' => [
            'type' => 'check',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:emailMeAtLogin',
        ],
        'password' => [
            'type' => 'password',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:newPassword',
            'table' => 'be_users',
        ],
        'password2' => [
            'type' => 'password',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:newPasswordAgain',
            'table' => 'be_users',
        ],
        'passwordCurrent' => [
            'type' => 'password',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:passwordCurrent',
            'table' => 'be_users',
        ],
        'avatar' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:be_users.avatar',
            'type' => 'avatar',
            'table' => 'be_users',
        ],
        'lang' => [
            'type' => 'language',
            'table' => 'be_users',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:language',
        ],
        'startModule' => [
            'type' => 'select',
            'itemsProcFunc' => SetupModuleController::class . '->renderStartModuleSelect',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:startModule',
        ],
        'titleLen' => [
            'type' => 'number',
            'class' => 'form-control-adapt',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:maxTitleLen',
        ],
        'edit_docModuleUpload' => [
            'type' => 'check',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:edit_docModuleUpload',
        ],
        'showHiddenFilesAndFolders' => [
            'type' => 'check',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:showHiddenFilesAndFolders',
        ],
        'copyLevels' => [
            'type' => 'number',
            'class' => 'form-control-adapt',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:copyLevels',
        ],
        'resetConfiguration' => [
            'type' => 'button',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:resetConfiguration',
            'buttonlabel' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:resetConfigurationButton',
            'confirm' => true,
            'confirmData' => [
                'message' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:setToStandardQuestion',
                'eventName' => 'setup:confirmation:response',
            ],
        ],
        'mfaProviders' => [
            'type' => 'mfa',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:mfaProviders',
        ],
        'backendTitleFormat' => [
            'type' => 'select',
            'label' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:backendTitleFormat',
            'items' => [
                'titleFirst' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:backendTitleFormat.titleFirst',
                'sitenameFirst' => 'LLL:EXT:setup/Resources/Private/Language/locallang.xlf:backendTitleFormat.sitenameFirst',
            ],
        ],
    ],
    'showitem' => '--div--;LLL:EXT:setup/Resources/Private/Language/locallang.xlf:personal_data,realName,email,emailMeAtLogin,avatar,lang,
            --div--;LLL:EXT:setup/Resources/Private/Language/locallang.xlf:accountSecurity,passwordCurrent,password,password2,mfaProviders,
            --div--;LLL:EXT:setup/Resources/Private/Language/locallang.xlf:opening,startModule,backendTitleFormat,
            --div--;LLL:EXT:setup/Resources/Private/Language/locallang.xlf:editFunctionsTab,titleLen,edit_docModuleUpload,showHiddenFilesAndFolders,copyLevels,
            --div--;LLL:EXT:setup/Resources/Private/Language/locallang.xlf:resetTab,resetConfiguration',
];
}

/**
 * Extension: news
 * File: /var/www/html/vendor/georgringer/news/ext_tables.php
 */

namespace {


use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$boot = static function (): void {
    $versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
    if ($versionInformation->getMajorVersion() < 12) {
        // CSH - context sensitive help
        foreach (['news', 'tag', 'link'] as $table) {
            // @extensionScannerIgnoreLine
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_news_domain_model_' . $table);

            // @extensionScannerIgnoreLine
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
                'tx_news_domain_model_' . $table,
                'EXT:news/Resources/Private/Language/locallang_csh_' . $table . '.xlf'
            );
        }

        // @extensionScannerIgnoreLine
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tt_content.pi_flexform.news_pi1.list',
            'EXT:news/Resources/Private/Language/locallang_csh_flexforms.xlf'
        );

        // @extensionScannerIgnoreLine
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'sys_file_reference',
            'EXT:news/Resources/Private/Language/locallang_csh_sys_file_reference.xlf'
        );
    }

    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\GeorgRinger\News\Domain\Model\Dto\EmConfiguration::class);

    // Extend user settings
    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['newsoverlay'] = [
        'label' => 'LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:usersettings.overlay',
        'type' => 'select',
        'itemsProcFunc' => \GeorgRinger\News\Hooks\ItemsProcFunc::class . '->user_categoryOverlay',
    ];
    if (!isset($GLOBALS['TYPO3_USER_SETTINGS']['showitem'])) {
        $GLOBALS['TYPO3_USER_SETTINGS']['showitem'] = '';
    }
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings('--div--;LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:pi1_title,newsoverlay');

    // Add tables to livesearch (e.g. "#news:fo" or "#newscat:fo")
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch']['news'] = 'tx_news_domain_model_news';
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch']['newstag'] = 'tx_news_domain_model_tag';

    /* ===========================================================================
        Register BE-Module for Administration
    =========================================================================== */
    if ($configuration->getShowAdministrationModule()) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'News',
            'web',
            'administration',
            '',
            [\GeorgRinger\News\Controller\AdministrationController::class => 'index,newNews,newCategory,newTag,newsPidListing,donate'],
            [
                'access' => 'user,group',
                'icon' => 'EXT:news/Resources/Public/Icons/module_administration.svg',
                'labels' => 'LLL:EXT:news/Resources/Private/Language/locallang_modadministration.xlf',
                'navigationComponentId' => $configuration->getHidePageTreeForAdministrationModule() ? '' : 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
                'inheritNavigationComponentFromMainModule' => false,
                'path' => '/module/web/NewsAdministration/',
            ]
        );
    }

    /* ===========================================================================
        Default configuration
    =========================================================================== */
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['orderByCategory'] = 'uid,title,tstamp,sorting';
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['orderByNews'] = 'tstamp,datetime,crdate,title' . ($configuration->getManualSorting() ? ',sorting' : '');
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['orderByTag'] = 'tstamp,crdate,title';
};

$boot();
unset($boot);
}

/**
 * Extension: kfv_template
 * File: /var/www/html/vendor/maxi-schmid/kfv-template/ext_tables.php
 */

namespace {

defined('TYPO3') or die('Access denied.');
}

#