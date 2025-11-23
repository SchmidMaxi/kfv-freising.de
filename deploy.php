<?php
namespace Deployer;

require 'recipe/typo3.php';

/**
 * Configuration
 */
set('writable_mode', 'chmod');
set('repository', 'git@github.com-typo3:SchmidMaxi/kfv-freising.de.git');
set('keep_releases', 3);
set('typo3_webroot', 'public');
set('ssh_multiplexing', false);
set('bin/php', '/usr/bin/php84');

// server configuration - STAGING
host('staging')
    ->set('hostname', 'www416.your-server.de')
    ->set('port', '222')
    ->set('remote_user', 'kfvvfr')
    ->set('deploy_path', '~/public_html/kfv-freising.de/v13/staging')
    ->set('labels', ['stage' => 'staging']);

// server configuration - PRODUCTION
host('production')
    ->set('hostname', 'www416.your-server.de')
    ->set('port', '222')
    ->set('remote_user', 'kfvvfr')
    ->set('deploy_path', '~/public_html/kfv-freising.de/v13/production')
    ->set('labels', ['stage' => 'production']);

# Writeable directories
add('writable_dirs', [
    'config',
    'var',
    '{{typo3_webroot}}/_assets',
    '{{typo3_webroot}}/fileadmin',
    '{{typo3_webroot}}/typo3temp',
]);

# Shared directories
add('shared_dirs', [
    'config',
    '{{typo3_webroot}}/fileadmin',
    '{{typo3_webroot}}/typo3temp',
    '{{typo3_webroot}}/_vt',
    'var'
]);

# Shared files
add('shared_files', [
    'env.php',
    '{{typo3_webroot}}/.htaccess',
    '{{typo3_webroot}}/.htpasswd'
]);

after('deploy:failed', 'deploy:unlock');
