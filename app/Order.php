<?php
declare(strict_types=1);

namespace App;

use RuntimeException;

final class Order
{
    public static function place(array $customer, string $paymentMethod, ?string $bkashTxn = null, ?string $screenshotPath = null): int
    {
        $items = Cart::items();
        if (!$items) throw new RuntimeException('Cart is empty.');

        $subtotal = Cart::subtotal();
        $shipping = Cart::shipping();
        $total    = $subtotal + $shipping;

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $orderId = Database::insert('orders', [
                'code'             => order_code(),
                'name'             => $customer['name'],
                'address'          => $customer['address'],
                'thana'            => $customer['thana'],
                'phone'            => $customer['phone'],
                'email'            => $customer['email'] ?? null,
                'payment_method'   => $paymentMethod,
                'bkash_txn'        => $bkashTxn,
                'bkash_screenshot' => $screenshotPath,
                'subtotal'         => $subtotal,
                'shipping'         => $shipping,
                'total'            => $total,
                'status'           => 'placed',
                'ip'               => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent'       => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]);
            foreach ($items as $i) {
                Database::insert('order_items', [
                    'order_id'   => $orderId,
                    'product_id' => (int) $i['id'],
                    'title'      => $i['title'],
                    'unit_price' => (float) $i['unit_price'],
                    'qty'        => (int) $i['qty'],
                    'line_total' => (float) $i['unit_price'] * (int) $i['qty'],
                ]);
                Product::decrementStock((int) $i['id'], (int) $i['qty']);
            }
            $pdo->commit();
            Content::log(null, 'order.placed', 'order', $orderId, ['code' => self::code($orderId), 'total' => $total]);
            Cart::clear();
            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function code(int $id): ?string
    {
        $r = Database::fetchOne('SELECT code FROM orders WHERE id=?', [$id]);
        return $r['code'] ?? null;
    }

    public static function find(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM orders WHERE id=?', [$id]);
    }

    public static function findByCode(string $code): ?array
    {
        return Database::fetchOne('SELECT * FROM orders WHERE code=?', [$code]);
    }

    public static function findForTracking(string $code, string $phone): ?array
    {
        $order = self::findByCode($code);
        if (!$order) return null;

        try {
            $storedPhone = Validation::phone((string) $order['phone']);
            $submittedPhone = Validation::phone($phone);
        } catch (\InvalidArgumentException $e) {
            return null;
        }

        return hash_equals($storedPhone, $submittedPhone) ? $order : null;
    }

    public static function items(int $orderId): array
    {
        return Database::fetchAll('SELECT * FROM order_items WHERE order_id=?', [$orderId]);
    }

    public static function transition(int $orderId, string $newStatus, ?int $userId = null): void
    {
        $o = self::find($orderId);
        if (!$o) throw new RuntimeException('Order not found.');
        $old = $o['status'];
        Database::update('orders', ['status' => $newStatus], 'id=:id', ['id' => $orderId]);
        Content::log($userId, 'order.transition', 'order', $orderId, ['from' => $old, 'to' => $newStatus]);

        $cfg = require __DIR__ . '/../config/config.php';
        $email = $o['email'] ?: $cfg['mail']['from_email'];
        $labels = [
            'confirmed'  => 'Order confirmed',
            'paid'       => 'Payment received',
            'processing' => 'Your order is being prepared',
            'shipped'    => 'Your order has shipped',
            'delivered'  => 'Order delivered',
            'completed'  => 'Order completed',
            'cancelled'  => 'Order cancelled',
        ];
        if (isset($labels[$newStatus]) && $o['email']) {
            $body = '<p>Hello ' . h($o['name']) . ',</p><p>Your order <strong>' . h($o['code']) . '</strong> is now: <strong>' . h($labels[$newStatus]) . '</strong>.</p>';
            if (in_array($newStatus, ['paid', 'completed'], true)) {
                $trackUrl = url('order-track.php?code=' . urlencode((string) $o['code']));
                $body .= '<p>To download an available book sample PDF, <a href="' . h($trackUrl) . '">track your order</a> using your order code and phone number.</p>';
            }
            Mail::send($o['email'], $o['name'], $labels[$newStatus] . ' — ' . $o['code'], Mail::wrap($labels[$newStatus], $body));
        }
    }
}
