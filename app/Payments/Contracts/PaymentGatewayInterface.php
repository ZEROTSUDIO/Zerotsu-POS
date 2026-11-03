<?php

namespace App\Payments\Contracts;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\DTOs\PaymentResultDTO;
use App\Payments\DTOs\WebhookResultDTO;

interface PaymentGatewayInterface
{
    /**
     * Inisiasi transaksi pembayaran baru untuk order tertentu.
     */
    public function createPayment(Order $order, PaymentMethod $method): PaymentResultDTO;

    /**
     * Cek status pembayaran ke server penyedia (status polling / verification).
     */
    public function verifyPayment(Payment $payment): PaymentResultDTO;

    /**
     * Memproses payload webhook/notifikasi dari gateway.
     */
    public function handleWebhook(array $payload): WebhookResultDTO;
}
