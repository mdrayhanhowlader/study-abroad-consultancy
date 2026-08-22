<?php
declare(strict_types=1);

namespace App\Payment;

final class GatewayFactory
{
    public static function make(?string $method = null): Gateway
    {
        $cfg = require __DIR__ . '/../../config/config.php';

        if ($method === 'sslcommerz') {
            $sslCfg = $cfg['sslcommerz'] ?? [];
            return new SSLCommerz([
                'store_id'       => $sslCfg['store_id'] ?? '',
                'store_password' => $sslCfg['store_pass'] ?? '',
                'sandbox'        => $sslCfg['sandbox'] ?? true,
                'success_url'    => $sslCfg['success_url'] ?? '',
                'fail_url'       => $sslCfg['fail_url'] ?? '',
                'cancel_url'     => $sslCfg['cancel_url'] ?? '',
            ]);
        }

        if ($method === 'bkash_api' || ($cfg['bkash']['mode'] ?? 'manual') === 'api') {
            return new BkashApi($cfg['bkash']);
        }

        return new Manual($cfg['bkash']);
    }
}
