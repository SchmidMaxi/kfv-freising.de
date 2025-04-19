<?php
define('TYPO3', 1);
error_reporting(E_ERROR | E_PARSE);

$classLoader = require './vendor/autoload.php';
$GLOBALS['TYPO3_CONF_VARS'] = [];
require './config/system/additional.php';

echo "Migrating FOR container flexform settings..." . PHP_EOL;

$containerSettings = [
    'container_2_columns' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="general">
            <language index="lDEF">
                <field index="width_column_md_1">
                    <value index="vDEF">col-lg-6</value>
                </field>
                <field index="width_column_md_2">
                    <value index="vDEF">col-lg-6</value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_xs">
            <language index="lDEF">
                <field index="width_column_xs_1">
                    <value index="vDEF">col-12</value>
                </field>
                <field index="width_column_xs_2">
                    <value index="vDEF">col-12</value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_sm">
            <language index="lDEF">
                <field index="width_column_sm_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_sm_2">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_lg">
            <language index="lDEF">
                <field index="width_column_lg_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_lg_2">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_xxl">
            <language index="lDEF">
                <field index="width_column_xxl_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_xxl_2">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="specials">
            <language index="lDEF">
                <field index="add_row">
                    <value index="vDEF"></value>
                </field>
                <field index="gutter">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_1">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_2">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
    'container_3_columns' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="general">
            <language index="lDEF">
                <field index="width_column_md_1">
                    <value index="vDEF">col-lg-4</value>
                </field>
                <field index="width_column_md_2">
                    <value index="vDEF">col-lg-4</value>
                </field>
                <field index="width_column_md_3">
                    <value index="vDEF">col-lg-4</value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_xs">
            <language index="lDEF">
                <field index="width_column_xs_1">
                    <value index="vDEF">col-12</value>
                </field>
                <field index="width_column_xs_2">
                    <value index="vDEF">col-12</value>
                </field>
                <field index="width_column_xs_3">
                    <value index="vDEF">col-12</value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_sm">
            <language index="lDEF">
                <field index="width_column_sm_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_sm_2">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_sm_3">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_lg">
            <language index="lDEF">
                <field index="width_column_lg_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_lg_2">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_lg_3">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_xxl">
            <language index="lDEF">
                <field index="width_column_xxl_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_xxl_2">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_xxl_3">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="specials">
            <language index="lDEF">
                <field index="add_row">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_1">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_2">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_3">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
    'container_4_columns' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="general">
            <language index="lDEF">
                <field index="width_column_md_1">
                    <value index="vDEF">col-lg-3</value>
                </field>
                <field index="width_column_md_2">
                    <value index="vDEF">col-lg-3</value>
                </field>
                <field index="width_column_md_3">
                    <value index="vDEF">col-lg-3</value>
                </field>
                <field index="width_column_md_4">
                    <value index="vDEF">col-lg-3</value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_xs">
            <language index="lDEF">
                <field index="width_column_xs_1">
                    <value index="vDEF">col-12</value>
                </field>
                <field index="width_column_xs_2">
                    <value index="vDEF">col-12</value>
                </field>
                <field index="width_column_xs_3">
                    <value index="vDEF">col-12</value>
                </field>
                <field index="width_column_xs_4">
                    <value index="vDEF">col-12</value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_sm">
            <language index="lDEF">
                <field index="width_column_sm_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_sm_2">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_sm_3">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_sm_4">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_lg">
            <language index="lDEF">
                <field index="width_column_lg_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_lg_2">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_lg_3">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_lg_4">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="columns_xxl">
            <language index="lDEF">
                <field index="width_column_xxl_1">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_xxl_2">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_xxl_3">
                    <value index="vDEF"></value>
                </field>
                <field index="width_column_xxl_4">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="specials">
            <language index="lDEF">
                <field index="add_row">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_1">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_2">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_3">
                    <value index="vDEF"></value>
                </field>
                <field index="add_column_4">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>'
];

// TODO: update list of page ids if final dump is available (in processor)
//$pageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
//$pageIds = $pageRepository->getPageIdsRecursive([1111], 20);

$forPageIds = array (
    0 => 1111,
    1 => 1112,
    2 => 1119,
    3 => 1135,
    4 => 1275,
    5 => 1276,
    6 => 1277,
    7 => 1278,
    8 => 1279,
    9 => 1280,
    10 => 1137,
    11 => 1281,
    12 => 1282,
    13 => 1283,
    14 => 1284,
    15 => 1285,
    16 => 1286,
    17 => 1460,
    18 => 1461,
    19 => 1462,
    20 => 1463,
    21 => 1464,
    22 => 1465,
    23 => 1466,
    24 => 1120,
    25 => 1133,
    26 => 1134,
    27 => 1121,
    28 => 1130,
    29 => 1131,
    30 => 1132,
    31 => 1331,
    32 => 1122,
    33 => 1129,
    34 => 1123,
    35 => 1126,
    36 => 1127,
    37 => 1305,
    38 => 1113,
    39 => 1114,
    40 => 1477,
    41 => 1476,
    42 => 1115,
    43 => 1336,
    44 => 1337,
    45 => 1116,
    46 => 1124,
    47 => 1125,
    48 => 1117,
    49 => 1327,
    50 => 1294,
    51 => 1295,
    52 => 1118,
    53 => 1354,
    54 => 1293,
    55 => 1289,
    56 => 1290,
    57 => 1291,
    58 => 1488,
);

foreach ($containerSettings as $cType => $flexformSettings) {
    echo "Migrating container flexform settings for CType: $cType..." . PHP_EOL;
    $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
        ->getQueryBuilderForTable('tt_content');
    $queryBuilder
        ->select('*')
        ->from('tt_content')
        ->where(
            $queryBuilder->expr()->in('pid', $forPageIds),
            $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter($cType))
        );
    $result = $queryBuilder->executeQuery()->fetchAllAssociative();
    echo "Found " . count($result) . " records." . PHP_EOL;
    foreach ($result as $row) {
        if(empty($row['pi_flexform'])) {
            $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
                ->getQueryBuilderForTable('tt_content');
            $queryBuilder->update('tt_content')
                ->where(
                    $queryBuilder->expr()->eq('uid', $row['uid'])
                )
                ->set('pi_flexform', $flexformSettings)
                ->executeStatement();
        }
    }
}

