<?php
namespace Deployer;

require 'recipe/laravel.php';

// Название проекта
set('application', 'jti');

// Репозиторий проекта
set('repository', 'https://gitlab+deploy-token-35:5LZyQp5MUQWjsJ_8EU13@gitlab.ibecsystems.kz/skritku/jti.git');

// [Optional] Allocate tty for git clone. Default value is false.
//set('git_tty', true);

// Общие файлы/директории которые перемещаются из релиза в релиз.
add('shared_files', ['.env']);

// Директории в которые может писать веб-сервер.
add('writable_dirs', ['storage', 'vendor']);
set('allow_anonymous_stats', false);

set('http_user', 'nginx');
set('default_timeout', 1000);

// Хост
// host('production')
//     ->hostname('34.67.122.192')
//     ->port(22)
//     ->set('branch', 'production')
//     ->stage('production')
//     ->user('deployer')
//     ->set('deploy_path', '/var/www/jti.kz')
//     ->set('composer_options', 'install --no-dev --verbose')
//     ->set('keep_releases', 3);

host('testing')
    ->hostname('188.0.151.149')
    ->port(2223)
    ->set('branch', 'master')
    ->stage('testing')
    ->user('jti')
    ->set('deploy_path', '/var/www/jti/data/www/jti.ibec.systems')
    ->set('composer_options', 'install --no-dev --verbose')
    ->set('keep_releases', 1);

// Build
task('build', function () {
    run('cd {{release_path}} && build');
});

// Заюзаем сиды если они добавлены
desc('Execute artisan db:seed');
task('artisan:db:seed', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan db:seed --force');
    writeln('<info>' . $output . '</info>');
});

// Сделаем composer dump-autoload
desc('Execute composer dump-autoload');
task('composer:dump-autoload', function() {
    run('cd {{release_path}} && composer dump-autoload --verbose');
});

// Удалим роут кэш так как юзаем Clasure.
desc('Execute artisan route:cache');
task('artisan:optimize', function () {
    run('echo scip route cache');
});

// Удалим остатки релизов с норм таймаутом по харду.
desc('Cleaning up old releases');
task('cleanup', function () {
    $releases = get('releases_list');
    $keep = get('keep_releases');
    $sudo  = get('cleanup_use_sudo') ? 'sudo' : '';
    $runOpts = [];
    if ($sudo) {
        $runOpts['tty'] = get('cleanup_tty', false);
    } else {
        $runOpts['timeout'] = 1000;
    }

    if ($keep === -1) {
        // Keep unlimited releases.
        return;
    }

    while ($keep > 0) {
        array_shift($releases);
        --$keep;
    }

    foreach ($releases as $release) {
        run("$sudo rm -rf {{deploy_path}}/releases/$release", $runOpts);
    }

    run("cd {{deploy_path}} && if [ -e release ]; then $sudo rm release; fi", $runOpts);
    run("cd {{deploy_path}} && if [ -h release ]; then $sudo rm release; fi", $runOpts);
});

// Удалим багнутный конфиг с Closure
task('deploy:writable', function() {
    run('echo do not writable');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');

// Delete because use routes with Closure route:cache - error;
after('artisan:config:cache', 'artisan:optimize');

// Clean up after unlock
after('deploy:unlock', 'cleanup');