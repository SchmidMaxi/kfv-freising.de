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

// KFV-Login (OIDC) via causal/oidc. Zugangsdaten kommen aus env.php (pro Server, nicht in Git).
// Ohne OIDC_CLIENT_ID bleibt die Frontend-Anmeldung deaktiviert.
$oidcBaseUrl = rtrim((string)getenv('OIDC_BASE_URL') ?: 'https://login.kfv-freising.de', '/');
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['oidc'] = array_replace(
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['oidc'] ?? [],
    [
        'enableFrontendAuthentication' => getenv('OIDC_CLIENT_ID') ? '1' : '0',
        // Backend bleibt bewusst lokal (eigene Konten + TYPO3-MFA), damit ein IAM-Ausfall das CMS nicht aussperrt
        'enableBackendAuthentication' => '0',
        'enablePasswordCredentials' => '0',
        'enableCodeVerifier' => '1',
        'frontendUserMustExistLocally' => '0',
        'reEnableFrontendUsers' => '0',
        'undeleteFrontendUsers' => '0',
        'oidcDisableCSRFProtection' => '0',
        'oidcRevokeAccessTokenAfterLogin' => '0',
        'oidcClientKey' => (string)getenv('OIDC_CLIENT_ID'),
        'oidcClientSecret' => (string)getenv('OIDC_CLIENT_SECRET'),
        // causal/oidc trennt die Liste an Kommas, der Separator gilt für die Übertragung an den IdP
        'oidcClientScopes' => 'openid,profile,email',
        'oidcClientScopeSeparator' => ' ',
        'oidcRedirectUri' => (string)getenv('OIDC_REDIRECT_URI'),
        'oidcEndpointAuthorize' => $oidcBaseUrl . '/oauth/authorize',
        'oidcEndpointToken' => $oidcBaseUrl . '/oauth/token',
        'oidcEndpointUserInfo' => $oidcBaseUrl . '/oauth/userinfo',
        'oidcEndpointLogout' => $oidcBaseUrl . '/oidc/end-session',
        'oidcEndpointRevoke' => '',
        'usersStoragePid' => (string)(getenv('OIDC_USERS_STORAGE_PID') ?: '53'),
        'usersDefaultGroup' => (string)getenv('OIDC_USERS_DEFAULT_GROUP'),
    ]
);
unset($oidcBaseUrl);

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
