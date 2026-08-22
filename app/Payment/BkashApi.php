<?php
declare(strict_types=1);

namespace App\Payment;

use RuntimeException;

final class BkashApi implements Gateway
{
    public function __construct(private array $config)
    {
        if (empty($this->config['app_key']) || empty($this->config['app_secret']) || empty($this->config['username']) || empty($this->config['password'])) {
            throw new RuntimeException('bKash API credentials are not configured. Set them in admin → Settings → bKash, then set BKASH_MODE=api in config.php.');
        }
    }

    public function name(): string { return 'bkash_api'; }

    public function charge(array $customer, float $amount, ?string $txnId, ?string $screenshotPath = null): array
    {
        $token = $this->token();
        $payment = $this->createPayment($token, 'MANAMI-' . bin2hex(random_bytes(4)), $amount);
        if (!$payment['ok']) return $payment;
        return [
            'ok'        => true,
            'reference' => $payment['paymentID'] ?? ($txnId ?? ''),
            'message'   => 'bKash payment created. Customer must approve in the bKash app.',
            'redirect'  => $payment['bkashURL'] ?? null,
        ];
    }

    private function base(): string
    {
        return !empty($this->config['sandbox'])
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }

    private function token(): string
    {
        $ch = curl_init($this->base() . '/tokenized/checkout/token/grant');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'username: ' . $this->config['username'],
                'password: ' . $this->config['password'],
            ],
            CURLOPT_POSTFIELDS     => json_encode([
                'app_key'    => $this->config['app_key'],
                'app_secret' => $this->config['app_secret'],
            ]),
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $d = json_decode((string) $body, true);
        if ($code >= 400 || empty($d['id_token'])) {
            throw new RuntimeException('bKash token grant failed (HTTP ' . $code . ').');
        }
        return $d['id_token'];
    }

    private function createPayment(string $token, string $merchantRef, float $amount): array
    {
        $ch = curl_init($this->base() . '/tokenized/checkout/create');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: ' . $token,
                'X-APP-Key: ' . $this->config['app_key'],
            ],
            CURLOPT_POSTFIELDS     => json_encode([
                'mode'                  => '0011',
                'payerReference'        => $merchantRef,
                'callbackURL'           => $this->config['callback_url'] ?? '',
                'amount'                => number_format($amount, 2, '.', ''),
                'currency'              => 'BDT',
                'intent'                => 'sale',
                'merchantInvoiceNumber' => $merchantRef,
            ]),
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        $d = json_decode((string) $body, true);
        if (($d['statusCode'] ?? '') !== '0000') {
            return ['ok' => false, 'message' => $d['statusMessage'] ?? 'bKash create failed'];
        }
        return ['ok' => true] + $d;
    }
}
