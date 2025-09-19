<?php

require(__DIR__.'/../../env.php');

// Configure the database
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'] = getenv("dbname");
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'] =  getenv("host");
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'] = getenv("user");
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'] = getenv("password");

// Vector-Tiles lokal aus var/tiles lesen:
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['feuerwehren']['vtProxy'] = [
    'mode'    => 'file',     // 'file' = lokale Dateien, 'http' = Proxy
    'storage' => 'var',      // 'var' = var/..., 'public' = public/...
    'fileBase'=> 'tiles',    // ergibt var/tiles/{z}/{x}/{y}.pbf
    // 'baseUrl' => 'https://api.maptiler.com/tiles/v3/{z}/{x}/{y}.pbf?key=qKjtLVvUmYMRbgrRm72l', // optional für Proxy/Fallback
];


if (getenv('IS_DDEV_PROJECT') == 'true') {
    $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
        $GLOBALS['TYPO3_CONF_VARS'],
        [
            'DB' => [
                'Connections' => [
                    'Default' => [
                        'dbname' => 'db',
                        'driver' => 'mysqli',
                        'host' => 'db',
                        'password' => 'db',
                        'port' => '3306',
                        'user' => 'db',
                    ],
                ],
            ],
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
