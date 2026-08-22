<?php
require_once __DIR__ . '/../app/bootstrap.php';

/**
 * SSLCommerz payment return page handler.
 * User is redirected here after payment (success/fail/cancel).
 */
$status = $_GET['status'] ?? $_POST['status'] ?? 'cancel';
$code   = $_GET['code'] ?? '';
$order  = $code ? App\Order::findByCode($code) : null;

if ($status === 'success' && $order) {
    $tranId = $_POST['tran_id'] ?? $_GET['tran_id'] ?? '';
    $cfg    = require __DIR__ . '/../config/config.php';
    $sslCfg = $cfg['sslcommerz'] ?? [];

    if (!empty($sslCfg['store_id']) && $tranId) {
        $result = App\Payment\SSLCommerz::validate($_POST ?: $_GET, $sslCfg);

        if ($result['ok']) {
            $pdo = App\Database::pdo();
            $pdo->beginTransaction();
            try {
                App\Database::insert('payment_transactions', [
                    'order_id'       => (int) $order['id'],
                    'method'         => 'sslcommerz',
                    'amount'         => (float) $order['total'],
                    'currency'       => 'BDT',
                    'txn_reference'  => $tranId,
                    'gateway_response' => json_encode($_POST ?: $_GET, JSON_UNESCAPED_UNICODE),
                    'status'         => 'verified',
                    'verified_at'    => date('Y-m-d H:i:s'),
                ]);

                App\Order::transition((int) $order['id'], 'paid');
                App\Pixel::logEvent('Purchase', [
                    'order_id' => (int) $order['id'],
                    'value'    => (float) $order['total'],
                    'currency' => 'BDT',
                ]);
                $pdo->commit();
            } catch (\Throwable $e) {
                $pdo->rollBack();
                error_log('SSLCommerz return error: ' . $e->getMessage());
            }
        }
    }

    header('Location: ../thanks.php?code=' . urlencode($order['code']), true, 302);
    exit;
}

if ($status === 'failed' && $order) {
    App\Database::insert('payment_transactions', [
        'order_id'       => (int) $order['id'],
        'method'         => 'sslcommerz',
        'amount'         => (float) $order['total'],
        'currency'       => 'BDT',
        'txn_reference'  => $_POST['tran_id'] ?? $_GET['tran_id'] ?? '',
        'gateway_response' => json_encode($_POST ?: $_GET, JSON_UNESCAPED_UNICODE),
        'status'         => 'failed',
    ]);

    App\Flash::set('error', 'Payment was not completed. Please try again.');
    header('Location: ../books.php#checkout', true, 302);
    exit;
}

header('Location: ../books.php', true, 302);
exit;
