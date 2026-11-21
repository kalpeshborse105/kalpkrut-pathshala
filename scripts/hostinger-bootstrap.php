<?php

/**
 * Hostinger Git auto-deploy bootstrap.
 * Hostinger publishes the repo into public_html (full Laravel tree).
 * This restores .env, storage, caches — without shell_exec/passthru.
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
} elseif (is_file($envTarget) && is_file($envSource) === false) {
    // Keep a durable backup outside the publish directory.
    @copy($envTarget, $envSource);
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

// When app already lives in public_html, do NOT create nested public_html symlink.
// Routing is handled by root .htaccess -> public/
$inPublicHtml = str_ends_with(str_replace('\\', '/', $root), '/public_html');
if (!$inPublicHtml) {
    $publicDir = $root . '/public';
    $publicHtml = $root . '/public_html';
    if (is_dir($publicDir) && (is_link($publicHtml) || !file_exists($publicHtml))) {
        if (is_link($publicHtml)) {
            @unlink($publicHtml);
        }
        if (@symlink($publicDir, $publicHtml)) {
            fwrite(STDOUT, "Linked public_html -> public\n");
        }
    }
}

// Ensure root rewrite exists for Hostinger docroot=public_html
$htaccess = $root . '/.htaccess';
if (!is_file($htaccess)) {
    file_put_contents($htaccess, <<<'HTA'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTA);
    fwrite(STDOUT, "Wrote root .htaccess -> public/\n");
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
