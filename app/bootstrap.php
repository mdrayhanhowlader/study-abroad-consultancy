<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$cfg = require __DIR__ . '/../config/config.php';
date_default_timezone_set($cfg['app']['timezone']);
ini_set('display_errors', $cfg['app']['debug'] ? '1' : '0');
error_reporting($cfg['app']['debug'] ? E_ALL : E_ALL & ~E_NOTICE & ~E_DEPRECATED);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

App\Auth::startSession();
App\I18n::boot();
