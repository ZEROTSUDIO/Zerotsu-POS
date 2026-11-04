<?php

namespace App\Payments\Gateways;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGatewayInterface;
use App\Payments\DTOs\PaymentResultDTO;
use App\Payments\DTOs\WebhookResultDTO;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransPaymentGateway implements PaymentGatewayInterface
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapUrl;
    protected string $coreApiUrl;

    public function __construct()
    {
        $this->serverKey = Config::get('midtrans.server_key', '');
        $this->clientKey = Config::get('midtrans.client_key', '');
        $this->isProduction = Config::get('midtrans.is_production', false);
        $this->snapUrl = Config::get('midtrans.snap_url');
        $this->coreApiUrl = Config::get('midtrans.core_api_url');
    }

    /**
     * Membuat Snap Transaction ke Midtrans Sandbox/Production.
     */
    public function createPayment(Order $order, PaymentMethod $method): PaymentResultDTO
    {
        if (empty($this->serverKey)) {
            Log::warning('Midtrans Server Key belum diatur di .env. Menggunakan fallback mock response.');
            return PaymentResultDTO::successful(
                status: PaymentStatus::PENDING,
                providerTransactionId: 'MOCK-SNAP-' . strtoupper(\Illuminate\Support\Str::random(12)),
                snapToken: 'snap-token-mock-' . \Illuminate\Support\Str::uuid(),
                redirectUrl: 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mock',
                paymentType: 'midtrans_snap',
                rawPayload: ['mock' => true, 'order_number' => $order->order_number]
            );
        }

        $items = $order->items->map(function ($item) {
            return [
                'id' => (string) ($item->product?->sku ?? $item->id),
                'price' => (int) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'name' => substr($item->product_name, 0, 50),
            ];
        })->values()->toArray();

        // Tambahkan tax jika ada
        if ($order->tax_amount > 0) {
            $items[] = [
                'id' => 'TAX',
                'price' => (int) $order->tax_amount,
                'quantity' => 1,
                'name' => 'Pajak (PB1 / PPN)',
            ];
        }

        // Tambahkan diskon jika ada (sebagai harga negatif)
        if ($order->discount_amount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => -(int) $order->discount_amount,
                'quantity' => 1,
                'name' => 'Diskon Promo',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $order->customer_name ?: 'Pelanggan Walk-in',
            ],
            'usage_limit' => 1,
        ];

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->timeout(30)
                ->connectTimeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->snapUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return PaymentResultDTO::successful(
                    status: PaymentStatus::PENDING,
                    providerTransactionId: null, // Midtrans trx id akan datang saat webhook/polling
                    snapToken: $data['token'] ?? null,
                    redirectUrl: $data['redirect_url'] ?? null,
                    paymentType: 'midtrans_snap',
                    rawPayload: $data
                );
            }

            Log::error('Midtrans Snap Error', ['status' => $response->status(), 'body' => $response->json()]);
            return PaymentResultDTO::failed(
                $response->json('error_messages.0') ?? 'Gagal membuat transaksi di Midtrans'
            );
        } catch (\Throwable $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return PaymentResultDTO::failed('Koneksi Midtrans gagal: ' . $e->getMessage());
        }
    }

    /**
     * Memverifikasi status order langsung ke Core API Midtrans.
     */
    public function verifyPayment(Payment $payment): PaymentResultDTO
    {
        $orderNumber = $payment->order->order_number;

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get("{$this->coreApiUrl}/{$orderNumber}/status");

            if ($response->successful()) {
                $data = $response->json();
                $internalStatus = $this->mapMidtransStatus(
                    $data['transaction_status'] ?? '',
                    $data['fraud_status'] ?? null
                );

                return PaymentResultDTO::successful(
                    status: $internalStatus,
                    providerTransactionId: $data['transaction_id'] ?? null,
                    paymentType: $data['payment_type'] ?? null,
                    rawPayload: $data
                );
            }

            return PaymentResultDTO::failed('Status transaksi tidak ditemukan di Midtrans');
        } catch (\Throwable $e) {
            return PaymentResultDTO::failed($e->getMessage());
        }
    }

    /**
     * Memproses payload Webhook dari Midtrans & memverifikasi Signature SHA-512.
     */
    public function handleWebhook(array $payload): WebhookResultDTO
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        // Hitung SHA-512 Signature: SHA512(order_id + status_code + gross_amount + ServerKey)
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        if (!hash_equals($expectedSignature, $signatureKey)) {
            Log::warning('Midtrans Webhook: Signature TIDAK VALID!', [
                'received' => $signatureKey,
                'expected' => $expectedSignature,
                'order_id' => $orderId
            ]);

            return WebhookResultDTO::invalid(
                errorMessage: 'Signature verification failed',
                rawPayload: $payload
            );
        }

        $trxStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? null;
        $internalStatus = $this->mapMidtransStatus($trxStatus, $fraudStatus);

        return new WebhookResultDTO(
            isValidSignature: true,
            orderNumber: $orderId,
            internalStatus: $internalStatus,
            providerTransactionId: $payload['transaction_id'] ?? null,
            providerStatusCode: $statusCode,
            paymentType: $payload['payment_type'] ?? null,
            grossAmount: (int) floatval($grossAmount),
            rawPayload: $payload
        );
    }

    /**
     * Memetakan status Midtrans ke Backed Enum PaymentStatus kita.
     */
    protected function mapMidtransStatus(string $transactionStatus, ?string $fraudStatus): PaymentStatus
    {
        return match ($transactionStatus) {
            'capture' => ($fraudStatus === 'challenge') ? PaymentStatus::PENDING : PaymentStatus::SETTLEMENT,
            'settlement' => PaymentStatus::SETTLEMENT,
            'pending' => PaymentStatus::PENDING,
            'deny' => PaymentStatus::DENIED,
            'expire' => PaymentStatus::EXPIRED,
            'cancel' => PaymentStatus::CANCELLED,
            'refund', 'partial_refund' => PaymentStatus::REFUNDED,
            default => PaymentStatus::PENDING,
        };
    }
}
