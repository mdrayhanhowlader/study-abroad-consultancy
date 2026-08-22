<?php
declare(strict_types=1);

namespace App\Payment;

interface Gateway
{
    public function name(): string;
    public function charge(array $customer, float $amount, ?string $txnId, ?string $screenshotPath = null): array;
}
