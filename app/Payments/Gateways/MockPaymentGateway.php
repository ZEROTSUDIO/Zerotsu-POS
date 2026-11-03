<?php

namespace App\Payments\Gateways;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGatewayInterface;
use App\Payments\DTOs\PaymentResultDTO;
use App\Payments\DTOs\WebhookResultDTO;
use Illuminate\Support\Str;

class MockPaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(Order $order, PaymentMethod $method): PaymentResultDTO
    {
        $mockTrxId = 'MOCK-' . strtoupper(Str::random(12));

        // Jika cash atau mock sukses, status langsung SETTLEMENT
        if ($method === PaymentMethod::CASH) {
            return PaymentResultDTO::successful(
                status: PaymentStatus::SETTLEMENT,
                providerTransactionId: $mockTrxId,
                paymentType: 'cash',
                rawPayload: ['method' => 'cash', 'amount' => $order->total_amount]
            );
        }

        // Simulasi online payment (PENDING dengan mock token)
        return PaymentResultDTO::successful(
            status: PaymentStatus::PENDING,
            providerTransactionId: $mockTrxId,
            snapToken: 'mock-token-' . Str::uuid(),
            redirectUrl: 'http://zerotsu-pos.test/mock-payment/' . $mockTrxId,
            paymentType: 'mock_sandbox',
            rawPayload: ['mock' => true, 'order_id' => $order->id]
        );
    }

    public function verifyPayment(Payment $payment): PaymentResultDTO
    {
        return PaymentResultDTO::successful(
            status: PaymentStatus::SETTLEMENT,
            providerTransactionId: $payment->provider_transaction_id,
            paymentType: $payment->payment_type
        );
    }

    public function handleWebhook(array $payload): WebhookResultDTO
    {
        $orderNumber = $payload['order_id'] ?? '';
        $statusStr = $payload['transaction_status'] ?? 'settlement';

        $status = match ($statusStr) {
            'settlement', 'capture' => PaymentStatus::SETTLEMENT,
            'pending' => PaymentStatus::PENDING,
            'expire' => PaymentStatus::EXPIRED,
            default => PaymentStatus::CANCELLED,
        };

        return new WebhookResultDTO(
            isValidSignature: true,
            orderNumber: $orderNumber,
            internalStatus: $status,
            providerTransactionId: $payload['transaction_id'] ?? ('MOCK-' . Str::random(10)),
            paymentType: 'mock',
            grossAmount: (int) ($payload['gross_amount'] ?? 0),
            rawPayload: $payload
        );
    }
}
