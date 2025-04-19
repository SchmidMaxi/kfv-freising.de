<?php
define('TYPO3', 1);
error_reporting(E_ERROR | E_PARSE);

$classLoader = require './vendor/autoload.php';
$GLOBALS['TYPO3_CONF_VARS'] = [];
require './config/system/additional.php';

echo "Migrating table tx_rflocations_locationtype_person_mm..." . PHP_EOL;

$queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
    ->getQueryBuilderForTable('tx_rflocations_locationtype_person_mm');
$queryBuilder
    ->select('*')
    ->from('tx_rflocations_locationtype_person_mm')
    ->orderBy('uid_local', 'ASC')
    ->addOrderBy('uid_foreign', 'ASC');
$result = $queryBuilder->executeQuery()->fetchAllAssociative();

$queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
    ->getQueryBuilderForTable('tx_rflocations_locationtype_person_mm');
$queryBuilder
    ->delete('tx_rflocations_locationtype_person_mm')
    ->executeStatement();

$previousUidLocal = -1;
$previousUidForeign = -1;
foreach ($result as $row) {
    if($previousUidLocal !== $row['uid_local'] || $previousUidForeign !== $row['uid_foreign']) {
        $previousUidLocal = $row['uid_local'];
        $previousUidForeign = $row['uid_foreign'];
        $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('tx_rflocations_locationtype_person_mm');
        $queryBuilder
            ->insert('tx_rflocations_locationtype_person_mm')
            ->values([
                'uid_local' => $row['uid_local'],
                'uid_foreign' => $row['uid_foreign'],
                'sorting' => $row['sorting'],
                'sorting_foreign' => $row['sorting_foreign']
            ]);
        $affectedRows = $queryBuilder->executeStatement();
    }
}
