<?php
define('TYPO3', 1);
error_reporting(E_ERROR | E_PARSE);

$classLoader = require './vendor/autoload.php';
$GLOBALS['TYPO3_CONF_VARS'] = [];
require './config/system/additional.php';

echo "Migrating flexform image files..." . PHP_EOL;

$listTypesAndFields = [
    'interaktiveelemente_ankermenu' => ['image' => 'settings.images'],
    'interaktiveelemente_badideen' => [
        'image' => 'settings.images',
        'grundriss' => 'settings.grundriss',
        'further' => 'settings.further'
    ],
    'interaktiveelemente_compareslider' => ['image' => 'settings.images'],
    'interaktiveelemente_imageoverlay' => ['image' => 'settings.images'],
    'interaktiveelemente_imageswitcher' => ['image' => 'settings.images'],
    'interaktiveelemente_imageteaser' => ['image' => 'settings.images'],
    'interaktiveelemente_infobox' => ['image' => 'settings.images'],
    'interaktiveelemente_videoteaser' => [
        'image' => 'settings.images',
        'video' => 'settings.video'
    ]
];

foreach($listTypesAndFields as $listType => $fields) {
    echo "Migrate $listType" . PHP_EOL;
    foreach($fields as $oldIdentifier => $newIdentifier) {
        echo "Old identifier: $oldIdentifier, new identifier: $newIdentifier" . PHP_EOL;
        $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder
            ->select('uid', 'pi_flexform')
            ->from('tt_content')
            ->where(
                "list_type = '$listType'"
            );
        $result = $queryBuilder->executeQuery()->fetchAllAssociative();

        foreach ($result as $row) {
            echo "TT-Content UID:" . $row['uid'] . ", ";
            $uidForeign = $row['uid'];
            $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getQueryBuilderForTable('sys_file_reference');
            $queryBuilder
                ->update('sys_file_reference')
                ->where(
                    $queryBuilder->expr()->eq('uid_foreign', $uidForeign),
                    $queryBuilder->expr()->eq('fieldname', $queryBuilder->createNamedParameter($oldIdentifier)),
                )
                ->set('tablenames', 'tt_content')
                ->set('fieldname', $newIdentifier)
                ->set('uid_foreign', $row['uid']);
            $affectedRows = $queryBuilder->executeStatement();
            echo "Affected rows: $affectedRows" . PHP_EOL;
        }
    }
}
