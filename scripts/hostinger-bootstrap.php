<?php

/**
 * Hostinger Git auto-deploy bootstrap.
 * Safe for CLI and one-shot web include after publish.
 */

$root = dirname(__DIR__);
$runningAsMain = (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__));

$finish = static function (string $message) use ($runningAsMain): void {
    fwrite(STDOUT, $message);
    if ($runningAsMain) {
        exit(0);
    }
};

chdir($root);

$normalizedRoot = str_replace('\\', '/', $root);
$isHostinger = is_dir('/home/u796881501') || str_contains($normalizedRoot, '/domains/');
if (!$isHostinger) {
    $finish("Hostinger bootstrap skipped (local environment)\n");
    return;
}

if (str_contains($normalizedRoot, '/.git_clones/')) {
    $finish("Hostinger bootstrap skipped (build clone stage)\n");
    return;
}

$home = getenv('HOME') ?: '/home/u796881501';
$envSource = rtrim($home, '/\\') . '/pathshala.production.env';
$envTarget = $root . '/.env';

if (!is_file($envTarget) && is_file($envSource)) {
    copy($envSource, $envTarget);
    fwrite(STDOUT, "Restored .env from {$envSource}\n");
} elseif (is_file($envTarget)) {
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

$inPublicHtml = str_ends_with($normalizedRoot, '/public_html');
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
    $finish("Bootstrap skipped artisan steps (.env or artisan missing)\n");
    return;
}

require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach ([
    ['migrate', ['--force' => true]],
    ['storage:link', ['--force' => true]],
    ['config:cache', []],
    ['route:cache', []],
    ['view:cache', []],
] as [$name, $params]) {
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

$finish("Hostinger bootstrap complete\n");
