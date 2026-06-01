<?php

// OIDC-Konfiguration für causal/oidc (KFV IAM)
// Echte Werte in config/system/additional.local.php eintragen (nicht in Git!)
//
// Nach dem Eintragen der Werte muss die Extension im TYPO3-Backend aktiviert
// werden: Admin Tools → Extensions → "oidc" aktivieren.
//
// IAM Discovery-Endpoint:
// https://login.kfv-freising.de/.well-known/openid-configuration

$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['oidc'] = [
    'oidcClientKey'         => 'YOUR_CLIENT_ID',
    'oidcClientSecret'      => 'YOUR_CLIENT_SECRET',
    'oidcEndpointAuthorize' => 'https://login.kfv-freising.de/oauth/authorize',
    'oidcEndpointToken'     => 'https://login.kfv-freising.de/oauth/token',
    'oidcEndpointUserInfo'  => 'https://login.kfv-freising.de/oauth/userinfo',
    'oidcEndpointLogout'    => 'https://login.kfv-freising.de/oidc/end-session',
    'oidcScopes'            => 'openid profile email',
    'enableFElogin'         => 1,
    'enableBElogin'         => 0,
    // Callback-URL muss im IAM registriert sein: https://kfv-freising.de/oidc/callback
];
