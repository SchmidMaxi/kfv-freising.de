<?php

require(__DIR__.'/../../env.php');

// Configure the database
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'] = getenv("dbname");
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'] =  getenv("host");
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'] = getenv("user");
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'] = getenv("password");

// Erlaubt Links auf die Feuerwehr-Detailansicht (tx_feuerwehren_karte[controller/action/feuerwehr])
// ohne cHash, damit clientseitiges JS (map-feuerwehren.js, D8) direkt darauf verlinken kann,
// ohne den cHash serverseitig neu berechnen zu müssen. Der Seiten-Cache bleibt dabei korrekt
// nach der vollständigen Parameter-Kombination granular (excludedParameters betrifft nur die
// cHash-Pflichtprüfung, nicht den Cache-Key).
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'] ?? [],
    [
        'tx_feuerwehren_karte[controller]',
        'tx_feuerwehren_karte[action]',
        'tx_feuerwehren_karte[feuerwehr]',
    ]
);

if (getenv('IS_DDEV_PROJECT') == 'true') {
    $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
        $GLOBALS['TYPO3_CONF_VARS'],
        [
            // This GFX configuration allows processing by installed ImageMagick 6
            'GFX' => [
                'processor' => 'ImageMagick',
                'processor_path' => '/usr/bin/',
                'processor_path_lzw' => '/usr/bin/',
            ],
            // This mail configuration sends all emails to mailpit
            'MAIL' => [
                'transport' => 'smtp',
                'transport_smtp_encrypt' => false,
                'transport_smtp_server' => 'localhost:1025',
            ],
            'SYS' => [
                'trustedHostsPattern' => '.*.*',
                'devIPmask' => '*',
                'displayErrors' => 1,
            ],
        ]
    );
}
