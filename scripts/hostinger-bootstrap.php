<?php

/**
 * Hostinger Git auto-deploy bootstrap.
 * Restores .env, storage dirs, public_html link, then runs artisan safely
 * without shell_exec/passthru (still disabled on Hostinger).
 */

$root = dirname(__DIR__);
chdir($root);

$isHostinger = is_dir('/home/u796881501') || str_contains(str_replace('\\', '/', $root), '/domains/');
if (!$isHostinger) {
    fwrite(STDOUT, "Hostinger bootstrap skipped (local environment)\n");
    exit(0);
}

$home = getenv('HOME') ?: '/home/u796881501';
$envSource = rtrim($home, '/\\') . '/pathshala.production.env';
$envTarget = $root . '/.env';

if (!is_file($envTarget) && is_file($envSource)) {
    copy($envSource, $envTarget);
    fwrite(STDOUT, "Restored .env from {$envSource}\n");
}

foreach ([
    'storage/app/public',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
] as $dir) {
    $path = $root . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

@chmod($root . '/storage', 0775);
@chmod($root . '/bootstrap/cache', 0775);

$publicDir = $root . '/public';
$publicHtml = $root . '/public_html';
if (is_dir($publicDir)) {
    if (is_link($publicHtml) || !file_exists($publicHtml)) {
        if (is_link($publicHtml)) {
            @unlink($publicHtml);
        }
        if (@symlink($publicDir, $publicHtml)) {
            fwrite(STDOUT, "Linked public_html -> public\n");
        }
    }
}

if (!is_file($root . '/artisan') || !is_file($envTarget)) {
    fwrite(STDOUT, "Bootstrap skipped artisan steps (.env or artisan missing)\n");
    exit(0);
}

require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$commands = [
    ['migrate', ['--force' => true]],
    ['storage:link', ['--force' => true]],
    ['config:cache', []],
    ['route:cache', []],
    ['view:cache', []],
];

foreach ($commands as [$name, $params]) {
    fwrite(STDOUT, "Running: php artisan {$name}\n");
    try {
        $exit = $kernel->call($name, $params);
        if ($exit !== 0) {
            fwrite(STDOUT, "Warning: artisan {$name} exited {$exit}\n");
        }
    } catch (Throwable $e) {
        fwrite(STDOUT, "Warning: artisan {$name} failed: {$e->getMessage()}\n");
    }
}

fwrite(STDOUT, "Hostinger bootstrap complete\n");
