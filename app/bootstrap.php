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
    $base = __DIR__ . '/';
    if (str_starts_with($class, 'App\\')) {
        $rel = str_replace('\\', '/', substr($class, 4)) . '.php';
        $path = $base . $rel;
        if (is_file($path)) { require $path; return; }
    }
});

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Csrf.php';
require_once __DIR__ . '/Flash.php';
require_once __DIR__ . '/Upload.php';
require_once __DIR__ . '/Mail.php';
require_once __DIR__ . '/Validation.php';
require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/I18n.php';
require_once __DIR__ . '/Content.php';
require_once __DIR__ . '/Product.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/Order.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/DataAdapter.php';
require_once __DIR__ . '/Payment/Gateway.php';
require_once __DIR__ . '/Payment/Manual.php';
require_once __DIR__ . '/Payment/BkashApi.php';
require_once __DIR__ . '/Payment/SSLCommerz.php';
require_once __DIR__ . '/Payment/GatewayFactory.php';
require_once __DIR__ . '/Lead.php';
require_once __DIR__ . '/SSW.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Pixel.php';

foreach (['Gateway', 'Manual', 'BkashApi', 'SSLCommerz', 'GatewayFactory'] as $alias) {
    if (!class_exists($alias, false) && class_exists('App\\Payment\\' . $alias, false)) {
        class_alias('App\\Payment\\' . $alias, $alias);
    }
}

App\Auth::startSession();
App\I18n::boot();
