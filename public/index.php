<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// After Hostinger Git publish, run bootstrap once when composer.lock changes.
$lockFile = __DIR__.'/../composer.lock';
$markerFile = __DIR__.'/../storage/framework/.hostinger_bootstrapped';
$bootstrapFile = __DIR__.'/../scripts/hostinger-bootstrap.php';
if (
    is_file($lockFile)
    && is_file($bootstrapFile)
    && (!is_file($markerFile) || filemtime($lockFile) > filemtime($markerFile))
) {
    try {
        ob_start();
        include $bootstrapFile;
        ob_end_clean();
        @touch($markerFile);
    } catch (Throwable $e) {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
