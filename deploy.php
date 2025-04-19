<?php
namespace Deployer;

require 'recipe/common.php';

/**
 * Configuration
 */
set('writable_mode', 'chmod');

set('repository', 'git@github.com:SchmidMaxi/kfv-freising.de.git');

set('keep_releases', 3);
set('typo3_webroot', 'public');
set('ssh_multiplexing', false);


// server configuration - STAGING
host('staging')
    ->set('hostname', 'www416.your-server.de')
    ->set('port', '222')
    ->set('remote_user', 'kfvvfr')
    ->set('deploy_path', '~/public_html/kfv-freising.de/Staging')
    ->set('labels', ['stage' => 'staging']);

// server configuration - PRODUCTION
host('production')
    ->set('hostname', 'www416.your-server.de')
    ->set('port', '222')
    ->set('remote_user', 'kfvvfr')
    ->set('deploy_path', '~/public_html/kfv-freising.de/Production')
    ->set('labels', ['stage' => 'production']);

# Shared directories
add('shared_dirs', [
    'config',
    '{{typo3_webroot}}/fileadmin',
    '{{typo3_webroot}}/typo3temp',
    'var'
]);

# Shared files
add('shared_files', [
    'env.php',
    'composer.phar',
    '{{typo3_webroot}}/.htaccess',
    '{{typo3_webroot}}/.htpasswd'
]);

# Writeable directories
add('writable_dirs', [
    'config',
    'var',
    '{{typo3_webroot}}/_assets',
    '{{typo3_webroot}}/fileadmin',
    '{{typo3_webroot}}/typo3temp',
]);

task('deploy:setup_typo3', function () {
    cd('{{release_path}}');
    run('/usr/bin/php83 composer.phar install --no-dev');
    run('/usr/bin/php83 composer.phar dump-autoload');
});

desc('Deploys your project');
task('deploy', [
    'deploy:prepare',
    'deploy:setup_typo3',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
