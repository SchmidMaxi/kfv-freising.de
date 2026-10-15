<?php

// KFV-Login (OIDC) – Vorlage für die Einträge in env.php (pro Server, nicht in Git, Deployer: shared file).
// Die eigentliche Extension-Konfiguration steht in config/system/additional.php und liest diese Werte.
// config/system/settings.php enthält bewusst keine OIDC-Zugangsdaten.
//
// Im IAM: Auth-Code-Client anlegen (confidential, oauth_clients.first_party = 1),
// Redirect-URI = OIDC_REDIRECT_URI, Host zu OIDC_ALLOWED_LOGOUT_HOSTS hinzufügen,
// Portal "typo3-kfv" mit dem Client verknüpfen (liefert portal_roles → FE-Gruppen).
//
// In env.php ergänzen:
//
//   'OIDC_BASE_URL'            => 'https://login.kfv-freising.de',
//   'OIDC_CLIENT_ID'           => '<client-uuid>',
//   'OIDC_CLIENT_SECRET'       => '<klartext-secret, wird bei Anlage einmalig angezeigt>',
//   'OIDC_REDIRECT_URI'        => 'https://www.kfv-freising.de/oidc/callback',
//   'OIDC_USERS_STORAGE_PID'   => '53',   // Ordner "Benutzer"
//   'OIDC_USERS_DEFAULT_GROUP' => '',     // optional: fe_groups-UID(s), kommasepariert
//
// FE-Gruppen für IAM-Rollen anlegen/aktualisieren (idempotent):
//   vendor/bin/typo3 kfv:oidc:setup-groups
//
// Bestehende fe_users einmalig mit der KFV-ID verknüpfen (CSV aus IAM: php artisan iam:export-kfv-ids):
//   vendor/bin/typo3 kfv:oidc:link-users /pfad/kfv-ids.csv          (Dry-Run)
//   vendor/bin/typo3 kfv:oidc:link-users /pfad/kfv-ids.csv --apply
