<?php

use Illuminate\Contracts\Console\Kernel;

define('LARAVEL_START', microtime(true));

if (PHP_VERSION_ID < 80300) {
    http_response_code(500);
    echo 'This app needs PHP 8.3 or newer. Plesk is running PHP '.PHP_VERSION.'.';
    exit;
}

if (! is_file(__DIR__.'/../vendor/autoload.php')) {
    http_response_code(500);
    echo 'Upload the vendor folder from your computer first.';
    exit;
}

require __DIR__.'/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$token = (string) env('INSTALL_TOKEN', '');
$given = (string) ($_GET['token'] ?? '');

if ($token === '' || ! hash_equals($token, $given)) {
    http_response_code(403);
    echo 'Forbidden. Add INSTALL_TOKEN to .env, then open /install.php?token=your-token';
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

$storage = __DIR__.'/../storage';
foreach ([
    $storage.'/app/public',
    $storage.'/framework/cache/data',
    $storage.'/framework/sessions',
    $storage.'/framework/views',
    $storage.'/logs',
    __DIR__.'/storage',
] as $dir) {
    if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
        echo "Could not create {$dir}\n";
    }
}

$exitCode = $kernel->call('migrate', ['--force' => true, '--seed' => true]);

echo $kernel->output();
echo $exitCode === 0
    ? "\nDone. Delete public/install.php now.\n"
    : "\nMigrate failed. Check DB_* in .env and Remote MySQL access for this server IP.\n";
