<?php
define('TYPO3', 1);
error_reporting(E_ERROR | E_PARSE);

$classLoader = require './vendor/autoload.php';
$GLOBALS['TYPO3_CONF_VARS'] = [];
require './config/system/additional.php';

echo "Migrating table sys_category_record_mm..." . PHP_EOL;

$queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
    ->getQueryBuilderForTable('sys_category_record_mm');
$queryBuilder
    ->select('*')
    ->from('sys_category_record_mm')
    ->orderBy('uid_local', 'ASC')
    ->addOrderBy('uid_foreign', 'ASC');
$result = $queryBuilder->executeQuery()->fetchAllAssociative();

$queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
    ->getQueryBuilderForTable('sys_category_record_mm');
$queryBuilder
    ->delete('sys_category_record_mm')
    ->executeStatement();

$previousUidLocal = -1;
$previousUidForeign = -1;
foreach ($result as $row) {
    if($row['tablenames'] !== 'tx_news_domain_model_news') {
        continue;
    }
    if($previousUidLocal !== $row['uid_local'] || $previousUidForeign !== $row['uid_foreign']) {
        $previousUidLocal = $row['uid_local'];
        $previousUidForeign = $row['uid_foreign'];
        $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category_record_mm');
        $queryBuilder
            ->insert('sys_category_record_mm')
            ->values([
                'uid_local' => $row['uid_local'],
                'uid_foreign' => $row['uid_foreign'],
                'tablenames' => $row['tablenames'],
                'fieldname' => $row['fieldname'],
                'sorting' => $row['sorting'],
                'sorting_foreign' => $row['sorting_foreign']
            ]);
        $affectedRows = $queryBuilder->executeStatement();
    }
}
