<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\Product;
use App\Payments\Contracts\PaymentGatewayInterface;
use App\Payments\Gateways\MidtransPaymentGateway;
use App\Payments\Gateways\MockPaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected MidtransPaymentGateway $midtransGateway,
        protected MockPaymentGateway $mockGateway
    ) {}

    /**
     * Membuat Order baru, snapshot item harga, dan menginisiasi pembayaran.
     *
     * @param array<int, array{product_id: int, quantity: int}> $cartItems
     */
    public function createOrder(
        array $cartItems,
        PaymentMethod $method,
        ?string $customerName = 'Walk-in Customer',
        ?int $cashierId = null,
        int $discountAmount = 0,
        int $taxAmount = 0,
        ?string $notes = null
    ): Order {
        return DB::transaction(function () use (
            $cartItems,
            $method,
            $customerName,
            $cashierId,
            $discountAmount,
            $taxAmount,
            $notes
        ) {
            $orderNumber = $this->generateOrderNumber();

            // Hitung subtotal & siapkan snapshot item
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($cartItems as $cartItem) {
                $product = Product::findOrFail($cartItem['product_id']);
                $qty = max(1, (int) $cartItem['quantity']);
                $itemSubtotal = $product->selling_price * $qty;
                $subtotal += $itemSubtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_cost' => $product->cost_price,
                    'unit_price' => $product->selling_price,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $totalAmount = max(0, $subtotal - $discountAmount + $taxAmount);

            // Buat record Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'cashier_id' => $cashierId,
                'customer_name' => $customerName ?: 'Walk-in Customer',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => OrderStatus::PENDING_PAYMENT,
                'notes' => $notes,
            ]);

            // Simpan snapshot Order Items
            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            // Inisiasi Pembayaran lewat Gateway
            $gateway = $this->resolveGateway($method);
            $paymentResult = $gateway->createPayment($order, $method);

            // Buat record Payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $method,
                'provider' => $method === PaymentMethod::CASH ? 'cash' : 'midtrans',
                'amount' => $totalAmount,
                'status' => $paymentResult->status,
                'provider_transaction_id' => $paymentResult->providerTransactionId,
                'payment_type' => $paymentResult->paymentType,
                'snap_token' => $paymentResult->snapToken,
                'snap_redirect_url' => $paymentResult->redirectUrl,
                'paid_at' => $paymentResult->status === PaymentStatus::SETTLEMENT ? now() : null,
            ]);

            // Jika pembayaran tunai langsung lunas
            if ($paymentResult->status === PaymentStatus::SETTLEMENT) {
                $order->update(['status' => OrderStatus::PAID]);
                $this->inventoryService->deductStockForOrder($order);

                PaymentEvent::create([
                    'payment_id' => $payment->id,
                    'event_type' => 'cash_settlement',
                    'payload' => ['amount' => $totalAmount, 'order' => $order->order_number],
                    'processed_at' => now(),
                ]);
            }

            return $order->fresh(['items', 'payments', 'latestPayment']);
        });
    }

    /**
     * Memproses penyelesaian pembayaran (Settlement) dari Webhook.
     * Menggunakan Idempotency check untuk mencegah eksekusi ganda.
     */
    public function settlePaymentFromWebhook(
        string $orderNumber,
        string $providerTransactionId,
        ?string $paymentType = null,
        array $rawPayload = []
    ): bool {
        return DB::transaction(function () use ($orderNumber, $providerTransactionId, $paymentType, $rawPayload) {
            $order = Order::where('order_number', $orderNumber)->lockForUpdate()->first();

            if (!$order) {
                return false;
            }

            // Cari atau update record payment
            $payment = Payment::where('order_id', $order->id)->latest()->first();

            if (!$payment) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => PaymentMethod::MIDTRANS_SNAP,
                    'provider' => 'midtrans',
                    'amount' => $order->total_amount,
                    'status' => PaymentStatus::PENDING,
                ]);
            }

            // Idempotency: Jika pembayaran sudah berstatus SETTLEMENT, jangan dobel proses!
            if ($payment->status === PaymentStatus::SETTLEMENT) {
                // Catat event notifikasi duplikat untuk audit trail
                PaymentEvent::create([
                    'payment_id' => $payment->id,
                    'event_type' => 'duplicate_notification_ignored',
                    'provider_event_id' => $providerTransactionId,
                    'payload' => $rawPayload,
                    'processed_at' => now(),
                ]);
                return true;
            }

            // 1. Update Payment
            $payment->update([
                'status' => PaymentStatus::SETTLEMENT,
                'provider_transaction_id' => $providerTransactionId,
                'payment_type' => $paymentType ?? $payment->payment_type,
                'paid_at' => now(),
            ]);

            // 2. Update Order
            $order->update([
                'status' => OrderStatus::PAID,
            ]);

            // 3. Potong stok produk di inventory movements
            $this->inventoryService->deductStockForOrder($order);

            // 4. Catat event resmi ke audit log
            PaymentEvent::create([
                'payment_id' => $payment->id,
                'event_type' => 'webhook_settlement',
                'provider_event_id' => $providerTransactionId,
                'payload' => $rawPayload,
                'processed_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Memproses webhook jika status pembayaran gagal atau kedaluwarsa.
     */
    public function updatePaymentStatusFromWebhook(
        string $orderNumber,
        PaymentStatus $status,
        ?string $providerTransactionId = null,
        array $rawPayload = []
    ): void {
        DB::transaction(function () use ($orderNumber, $status, $providerTransactionId, $rawPayload) {
            $order = Order::where('order_number', $orderNumber)->first();
            if (!$order) {
                return;
            }

            $payment = Payment::where('order_id', $order->id)->latest()->first();
            if ($payment && !$payment->status->isTerminal()) {
                $payment->update([
                    'status' => $status,
                    'provider_transaction_id' => $providerTransactionId ?? $payment->provider_transaction_id,
                ]);

                if ($status === PaymentStatus::EXPIRED || $status === PaymentStatus::CANCELLED) {
                    $order->update(['status' => OrderStatus::CANCELLED]);
                }

                PaymentEvent::create([
                    'payment_id' => $payment->id,
                    'event_type' => 'webhook_' . $status->value,
                    'provider_event_id' => $providerTransactionId,
                    'payload' => $rawPayload,
                    'processed_at' => now(),
                ]);
            }
        });
    }

    protected function resolveGateway(PaymentMethod $method): PaymentGatewayInterface
    {
        return match ($method) {
            PaymentMethod::CASH => $this->mockGateway,
            PaymentMethod::MIDTRANS_SNAP, PaymentMethod::MIDTRANS_QRIS => $this->midtransGateway,
        };
    }

    protected function generateOrderNumber(): string
    {
        $prefix = 'ZPOS-' . date('Ymd') . '-';
        $todayCount = Order::whereDate('created_at', today())->count() + 1;
        return $prefix . str_pad((string) $todayCount, 4, '0', STR_PAD_LEFT);
    }
}
