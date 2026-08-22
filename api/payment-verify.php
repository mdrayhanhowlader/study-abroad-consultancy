<?php
require_once __DIR__ . '/../app/bootstrap.php';

/**
 * SSLCommerz payment callback/verification endpoint.
 * Called by SSLCommerz after payment completion.
 */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit;
}

$cfg = require __DIR__ . '/../config/config.php';
$sslCfg = $cfg['sslcommerz'] ?? [];

if (empty($sslCfg['store_id'])) {
    error_log('SSLCommerz callback: store_id not configured');
    exit;
}

$result = App\Payment\SSLCommerz::validate($_POST, $sslCfg);

if (!$result['ok']) {
    error_log('SSLCommerz validation failed: ' . ($result['message'] ?? 'unknown'));
    echo 'FAILED';
    exit;
}

$tranId = $_POST['tran_id'] ?? '';
$order  = App\Database::fetchOne("SELECT * FROM orders WHERE code LIKE ?", ['%' . $tranId . '%']);

if ($order) {
    $pdo = App\Database::pdo();
    $pdo->beginTransaction();
    try {
        App\Database::insert('payment_transactions', [
            'order_id'       => (int) $order['id'],
            'method'         => 'sslcommerz',
            'amount'         => (float) $order['total'],
            'currency'       => 'BDT',
            'txn_reference'  => $tranId,
            'gateway_response' => json_encode($_POST, JSON_UNESCAPED_UNICODE),
            'status'         => 'verified',
            'verified_at'    => date('Y-m-d H:i:s'),
        ]);

        App\Order::transition((int) $order['id'], 'paid');

        $pdo->commit();
        echo 'SUCCESS';
    } catch (\Throwable $e) {
        $pdo->rollBack();
        error_log('SSLCommerz callback error: ' . $e->getMessage());
        echo 'FAILED';
    }
} else {
    echo 'FAILED';
}
