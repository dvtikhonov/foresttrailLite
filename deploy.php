<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'vendor/deployer/recipes/recipe/npm.php';

// Project name
set('application', 'foresttrail');

// Project repository
set('repository', 'git@github.com:tikhonovkv/foresttrail.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);
set('ssh_type', 'native');
set('ssh_multiplexing', true);
set('writable_use_sudo', true);
set('keep_releases', 1);

// Shared files/dirs between deploys
add('shared_files', [
    '.env'
]);
add('shared_dirs', [
    'storage',
    //'node_modules',
    'vendor'
]);

// Writable dirs by web server 
add('writable_dirs', [
    'bootstrap/cache',
    'vendor/php-ai',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/api-docs',
    'storage/framework',
    'storage/clockwork',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs'
]);


// Hosts

host('46.229.215.116')
    ->set('writable_mode', 'chmod')
    ->user('developer')
    ->set('deploy_path', '/home/developer/sites/foresttrail');


// Tasks
task('fpm:restart', function () {
    run('sudo service nginx restart && sudo service php7.2-fpm restart');
})->desc('Restart php fpm');

task('swagger:generate', function () {
    run('cd {{release_path}} && php artisan l5-swagger:generate');
})->desc('Swagger generate');

task('npm:production', 'npm run production')->desc('Build app');
task('chmod:storage', 'sudo chmod 777 -R storage')->desc('Build app');
task('chmod:vendor/php-ai', 'sudo chmod 777 -R vendor/php-ai')->desc('Build app');
task('chmod:node_modules', 'sudo chmod 777 -R node_modules')->desc('Build app');

//task('deploy:install-npm', function() {
////    run('cd {{release_path}} && rm node_modules');
////    run('cd {{release_path}} && npm cache clear --force');
//    run('cd {{release_path}} && npm i');
//})->desc('Install npm');

task('upload:env', function () {
    upload('.env.production', '{{deploy_path}}/shared/.env');
})->desc('Environment setup');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

//before('deploy:symlink', 'artisan:migrate');

task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'upload:env',
    'deploy:shared',
    'deploy:vendors',
    'npm:install',
    'npm:production',
    'deploy:writable',
    'artisan:view:clear',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:migrate',
    'swagger:generate',
    'deploy:symlink',
    'deploy:unlock',
    'fpm:restart',
    'chmod:storage',
    'chmod:vendor/php-ai',
    //'chmod:node_modules',
    'cleanup',
]);