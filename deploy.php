<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/cachetool.php';

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

host('production')
    ->hostname('109.233.110.131')
    ->port(2223)
    ->set('branch', 'production')
    ->stage('production')
    ->user('partner360')
    ->set('deploy_path', '/home/partner360/www/backend.partner360.kz')
    ->set('composer_options', 'install --no-dev --verbose')
    ->set('keep_releases', 3);

host('testing')
    ->hostname('188.0.151.149')
    ->port(2223)
    ->set('branch', 'master')
    ->stage('testing')
    ->user('jti')
    ->set('deploy_path', '/var/www/jti/data/www/jti.ibec.systems')
    ->set('composer_options', 'install --no-dev --verbose')
    ->set('keep_releases', 2);

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

/**
 * Clear opcache cache
 */
desc('Clearing OPcode cache');
task('cachetool:clear:opcache', function () {
    $releasePath = get('release_path');
    $options = get('cachetool');
    $fullOptions = get('cachetool_args');

    if (strlen($fullOptions) > 0) {
        $options = "{$fullOptions}";
    } elseif (strlen($options) > 0) {
        $options = "--fcgi={$options}";
    }

    cd($releasePath);
    $hasCachetool = run("if [ -e $releasePath/{{bin/cachetool}} ]; then echo 'true'; fi");

    if ('true' !== $hasCachetool) {
        run("curl -sO https://gordalina.github.io/cachetool/downloads/{{bin/cachetool}}");
    }

    run("{{bin/php}} {{bin/cachetool}} opcache:reset {$options}");
})->onStage('production');

// Удалим багнутный конфиг с Closure
task('deploy:writable', function() {
    run('echo do not writable');
});

desc('Clear opcache for production');
task('opcache:clear', function () {
    run('echo do not writable');
})->onStage('production');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');

// Cache clear
after('success', 'cachetool:clear:opcache');

// View clear because Redis.
after('success','artisan:view:clear');

// Cache clear because Redis.
after('success','artisan:cache:clear');

// Config Cache clear because Redis.
after('success','artisan:config:cache');