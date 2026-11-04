<?php

namespace App\Livewire;

use App\Enums\PaymentMethod;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.pos')]
#[Title('ZerotsuPOS // Terminal 01')]
class PosTerminal extends Component
{
    public string $terminalId = 'TERMINAL-01';
    public string $shiftNumber = 'SHIFT-01';

    // Phase 3.2 Filtering & Search State
    public string $search = '';
    public ?int $selectedCategory = null;

    // Phase 3.3 Cart & Financial Engine State
    public array $cart = [];
    public string $customerName = 'Walk-in Guest';
    public string $orderNotes = '';
    
    // Discount & Tax Settings
    public string $discountType = 'fixed'; // 'fixed' | 'percent'
    public int $discountValue = 0;
    public bool $enableTax = true; // 10% PB1 café tax enabled by default
    public int $taxRate = 10;
    
    // Phase 3.4 & 3.5 Checkout & Payment State
    public bool $showCheckoutModal = false;
    public string $selectedPaymentMethod = 'cash'; // 'cash' | 'midtrans_snap'
    public int $cashTendered = 0;
    public bool $showReceiptModal = false;
    public ?int $lastCompletedOrderId = null;
    public array $lastReceiptData = [];

    // Phase 3.5 Midtrans Snap State
    public ?string $pendingOrderNumber = null;
    public ?string $pendingSnapToken = null;
    public ?string $pendingRedirectUrl = null;
    public bool $showSnapWaitingModal = false;
    public bool $showSnapSimulatorModal = false;

    // Phase 3.7 Developer Testing Toolbar
    public bool $showDevToolbar = false;

    // UI Drawer Helpers
    public bool $showDiscountOptions = false;
    public bool $showCustomerModal = false;
    public ?string $feedbackMessage = null;

    public function selectCategory(?int $categoryId = null): void
    {
        $this->selectedCategory = $categoryId;
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    /**
     * Add product to cart with strict stock validation.
     */
    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_active) {
            return;
        }

        // Prevent adding out-of-stock items
        if ($product->stock <= 0) {
            $this->feedbackMessage = "Barang [{$product->name}] habis (Stock: 0).";
            return;
        }

        $currentQty = $this->cart[$productId]['quantity'] ?? 0;

        // Prevent adding more than available stock
        if ($currentQty >= $product->stock) {
            $this->feedbackMessage = "Batas stok maksimal tercapai untuk [{$product->name}] (Max: {$product->stock}).";
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => (int) $product->selling_price,
                'cost' => (int) $product->cost_price,
                'quantity' => 1,
                'stock' => (int) $product->stock,
                'category' => $product->category?->name ?? 'General',
            ];
        }

        $this->feedbackMessage = null;
    }

    /**
     * Increment item quantity with stock boundary check.
     */
    public function incrementQuantity(int $productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        if ($this->cart[$productId]['quantity'] >= $product->stock) {
            $this->feedbackMessage = "Stok maksimal untuk [{$product->name}] adalah {$product->stock}.";
            return;
        }

        $this->cart[$productId]['quantity']++;
        $this->feedbackMessage = null;
    }

    /**
     * Decrement item quantity or remove if reaching 0.
     */
    public function decrementQuantity(int $productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
        } else {
            $this->removeFromCart($productId);
        }

        $this->feedbackMessage = null;
    }

    /**
     * Remove item entirely from cart.
     */
    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
        $this->feedbackMessage = null;
    }

    /**
     * Clear all cart items and reset discounts.
     */
    public function clearCart(): void
    {
        $this->cart = [];
        $this->discountValue = 0;
        $this->discountType = 'fixed';
        $this->orderNotes = '';
        $this->feedbackMessage = null;
    }

    /**
     * Toggle PB1 / PPN tax on/off.
     */
    public function toggleTax(): void
    {
        $this->enableTax = !$this->enableTax;
    }

    /**
     * Apply preset or custom discount.
     */
    public function applyDiscount(string $type, int $value): void
    {
        $this->discountType = in_array($type, ['fixed', 'percent']) ? $type : 'fixed';
        $this->discountValue = max(0, $value);
        $this->showDiscountOptions = false;
    }

    /**
     * Clear current discount.
     */
    public function clearDiscount(): void
    {
        $this->discountValue = 0;
        $this->discountType = 'fixed';
        $this->showDiscountOptions = false;
    }

    // ══════════════════════════════════════════════════════════
    // PHASE 3.4 & 3.5 CHECKOUT & PAYMENT INTEGRATIONS
    // ══════════════════════════════════════════════════════════

    #[On('trigger-checkout')]
    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) {
            $this->feedbackMessage = 'Keranjang kosong. Pilih item menu terlebih dahulu!';
            return;
        }

        $this->cashTendered = $this->grandTotal; // default to exact amount due
        $this->showCheckoutModal = true;
        $this->feedbackMessage = null;
    }

    #[On('close-modals')]
    public function closeCheckoutModal(): void
    {
        $this->showCheckoutModal = false;
        $this->showReceiptModal = false;
        $this->showSnapSimulatorModal = false;
    }

    public function setCashTendered(int $amount): void
    {
        $this->cashTendered = max(0, $amount);
    }

    public function addCashTendered(int $amount): void
    {
        $this->cashTendered += max(0, $amount);
    }

    /**
     * Atomic Cash Settlement using OrderService + InventoryService.
     */
    public function processCashPayment(OrderService $orderService): void
    {
        if (empty($this->cart)) {
            $this->feedbackMessage = 'Keranjang belanja kosong!';
            return;
        }

        if ($this->cashTendered < $this->grandTotal) {
            $this->feedbackMessage = 'Nominal uang tunai kurang dari total tagihan!';
            return;
        }

        $cartItems = [];
        foreach ($this->cart as $item) {
            $cartItems[] = [
                'product_id' => (int) $item['id'],
                'quantity' => (int) $item['quantity'],
            ];
        }

        try {
            $order = $orderService->createOrder(
                cartItems: $cartItems,
                method: PaymentMethod::CASH,
                customerName: $this->customerName ?: 'Walk-in Guest',
                cashierId: auth()->id(),
                discountAmount: $this->discountAmount,
                taxAmount: $this->taxAmount,
                notes: $this->orderNotes
            );

            // Store snapshot for Receipt Modal Dossier
            $this->lastReceiptData = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'created_at' => $order->created_at->format('d M Y · H:i') . ' WIB',
                'items' => collect($this->cart)->values()->toArray(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'tax_amount' => $order->tax_amount,
                'total_amount' => $order->total_amount,
                'cash_tendered' => $this->cashTendered,
                'change_amount' => max(0, $this->cashTendered - $order->total_amount),
                'payment_method' => 'TUNAI (CASH)',
                'cashier_name' => auth()->user()->name ?? 'Zerotsu Cashier',
            ];

            $this->lastCompletedOrderId = $order->id;

            // Reset cart state
            $this->clearCart();
            $this->showCheckoutModal = false;
            $this->showReceiptModal = true;
            $this->feedbackMessage = null;

        } catch (\Throwable $e) {
            $this->feedbackMessage = 'Gagal memproses pembayaran: ' . $e->getMessage();
        }
    }

    /**
     * Phase 3.5: Midtrans Snap Payment Initiation
     */
    public function processMidtransPayment(OrderService $orderService): void
    {
        if (empty($this->cart)) {
            $this->feedbackMessage = 'Keranjang belanja kosong!';
            return;
        }

        $cartItems = [];
        foreach ($this->cart as $item) {
            $cartItems[] = [
                'product_id' => (int) $item['id'],
                'quantity' => (int) $item['quantity'],
            ];
        }

        try {
            $order = $orderService->createOrder(
                cartItems: $cartItems,
                method: PaymentMethod::MIDTRANS_SNAP,
                customerName: $this->customerName ?: 'Walk-in Guest',
                cashierId: auth()->id(),
                discountAmount: $this->discountAmount,
                taxAmount: $this->taxAmount,
                notes: $this->orderNotes
            );

            $this->pendingOrderNumber = $order->order_number;
            $this->pendingSnapToken = $order->latestPayment?->snap_token;
            $this->pendingRedirectUrl = $order->latestPayment?->snap_redirect_url;

            $this->showCheckoutModal = false;
            $this->showSnapWaitingModal = true;
            $this->feedbackMessage = null;

            // Dispatch browser event to launch Midtrans Snap JS popup
            $this->dispatch('open-snap-modal', [
                'snapToken' => $this->pendingSnapToken,
                'orderNumber' => $this->pendingOrderNumber,
                'redirectUrl' => $this->pendingRedirectUrl,
            ]);

        } catch (\Throwable $e) {
            $this->feedbackMessage = 'Gagal membuat pembayaran Midtrans: ' . $e->getMessage();
        }
    }

    #[On('snap-payment-success')]
    public function handleSnapSuccess(OrderService $orderService, ?array $result = null, ?string $orderNumber = null, ?array $payload = null): void
    {
        $targetOrder = $orderNumber ?? ($payload['orderNumber'] ?? $this->pendingOrderNumber);
        $res = $result ?? ($payload['result'] ?? []);
        $trxId = $res['transaction_id'] ?? ('SNAP-' . strtoupper(Str::random(10)));
        $paymentType = $res['payment_type'] ?? 'midtrans_qris';

        if ($targetOrder) {
            $orderService->settlePaymentFromWebhook(
                orderNumber: $targetOrder,
                providerTransactionId: $trxId,
                paymentType: $paymentType,
                rawPayload: $payload ?? ['result' => $res, 'orderNumber' => $targetOrder]
            );

            $this->completeOnlineOrderReceipt($targetOrder, 'MIDTRANS (QRIS/VA)');
        }
    }

    #[On('snap-payment-pending')]
    public function handleSnapPending(?array $result = null, ?string $orderNumber = null, ?array $payload = null): void
    {
        $this->showSnapWaitingModal = true;
        $this->feedbackMessage = 'Menunggu penyelesaian pembayaran oleh pelanggan (QRIS / Virtual Account)...';
    }

    #[On('snap-payment-error')]
    public function handleSnapError(?array $result = null, ?string $orderNumber = null, ?array $payload = null): void
    {
        $this->showSnapWaitingModal = false;
        $this->feedbackMessage = 'Pembayaran Midtrans gagal atau dibatalkan.';
    }

    #[On('snap-payment-closed')]
    public function handleSnapClosed(): void
    {
        // Modal popup closed by customer/cashier
    }

    #[On('show-snap-simulator')]
    public function showSnapSimulator(?string $snapToken = null, ?string $orderNumber = null): void
    {
        $this->showSnapSimulatorModal = true;
    }

    /**
     * Phase 3.6: Polling check for pending order payment settlement
     */
    public function checkPendingPaymentStatus(): void
    {
        if (!$this->pendingOrderNumber) {
            return;
        }

        $order = Order::where('order_number', $this->pendingOrderNumber)->with('latestPayment')->first();
        if (!$order) {
            return;
        }

        if ($order->status === \App\Enums\OrderStatus::PAID) {
            $paymentType = $order->latestPayment?->payment_type ?? 'MIDTRANS ONLINE';
            $this->completeOnlineOrderReceipt($this->pendingOrderNumber, strtoupper($paymentType));
        } elseif (in_array($order->status, [\App\Enums\OrderStatus::CANCELLED, \App\Enums\OrderStatus::EXPIRED])) {
            $this->showSnapWaitingModal = false;
            $this->showSnapSimulatorModal = false;
            $this->pendingOrderNumber = null;
            $this->pendingSnapToken = null;
            $this->feedbackMessage = "Transaksi [{$order->order_number}] berstatus {$order->status->value}.";
        }
    }

    /**
     * Simulate Webhook Settlement in Developer Mode
     */
    public function simulateSnapSettlement(OrderService $orderService): void
    {
        if (!$this->pendingOrderNumber) {
            return;
        }

        $orderService->settlePaymentFromWebhook(
            orderNumber: $this->pendingOrderNumber,
            providerTransactionId: 'SIM-SNAP-' . strtoupper(Str::random(10)),
            paymentType: 'midtrans_qris',
            rawPayload: ['simulated' => true, 'timestamp' => now()->toIso8601String()]
        );

        $this->completeOnlineOrderReceipt($this->pendingOrderNumber, 'MIDTRANS QRIS (SIMULATED)');
    }

    public function cancelPendingOrder(): void
    {
        if ($this->pendingOrderNumber) {
            $order = Order::where('order_number', $this->pendingOrderNumber)->first();
            if ($order && $order->status === \App\Enums\OrderStatus::PENDING_PAYMENT) {
                $order->update(['status' => \App\Enums\OrderStatus::CANCELLED]);
            }
        }

        $this->showSnapWaitingModal = false;
        $this->showSnapSimulatorModal = false;
        $this->pendingOrderNumber = null;
        $this->pendingSnapToken = null;
        $this->feedbackMessage = 'Pembayaran online dibatalkan.';
    }

    // ══════════════════════════════════════════════════════════
    // PHASE 3.7 DEVELOPER TOOLBAR & WEBHOOK SIMULATOR
    // ══════════════════════════════════════════════════════════

    public function toggleDevToolbar(): void
    {
        $this->showDevToolbar = !$this->showDevToolbar;
    }

    /**
     * Simulate incoming Midtrans Webhook notification (Settlement, Expire, Cancel)
     */
    public function simulateWebhookNotification(string $targetStatus, OrderService $orderService): void
    {
        $targetOrderNumber = $this->pendingOrderNumber;

        if (!$targetOrderNumber) {
            $recent = Order::where('status', \App\Enums\OrderStatus::PENDING_PAYMENT)->latest()->first();
            if ($recent) {
                $targetOrderNumber = $recent->order_number;
            } else {
                $this->feedbackMessage = '[DEV TOOLBAR] Tidak ada pesanan aktif berstatus PENDING PAYMENT.';
                return;
            }
        }

        $order = Order::where('order_number', $targetOrderNumber)->first();
        if (!$order) {
            $this->feedbackMessage = "[DEV TOOLBAR] Order {$targetOrderNumber} tidak ditemukan.";
            return;
        }

        $trxId = 'SIM-WEBHOOK-' . strtoupper(Str::random(8));

        if ($targetStatus === 'settlement') {
            $orderService->settlePaymentFromWebhook(
                orderNumber: $order->order_number,
                providerTransactionId: $trxId,
                paymentType: 'qris',
                rawPayload: [
                    'order_id' => $order->order_number,
                    'transaction_status' => 'settlement',
                    'payment_type' => 'qris',
                    'gross_amount' => (string) $order->total_amount,
                    'transaction_id' => $trxId,
                    'simulated_by' => 'POS Dev Toolbar',
                ]
            );

            $this->feedbackMessage = "⚡ [DEV SIMULATOR] Webhook 'settlement' berhasil diproses untuk {$order->order_number}! Stok terpotong.";
            $this->completeOnlineOrderReceipt($order->order_number, 'MIDTRANS QRIS (SIMULATED)');
        } elseif ($targetStatus === 'expire') {
            $orderService->updatePaymentStatusFromWebhook(
                orderNumber: $order->order_number,
                status: \App\Enums\PaymentStatus::EXPIRED,
                providerTransactionId: $trxId,
                rawPayload: ['order_id' => $order->order_number, 'transaction_status' => 'expire']
            );
            $this->cancelPendingOrder();
            $this->feedbackMessage = "⚡ [DEV SIMULATOR] Webhook 'expire' diproses. Order {$order->order_number} ditandai EXPIRED.";
        } elseif ($targetStatus === 'cancel') {
            $orderService->updatePaymentStatusFromWebhook(
                orderNumber: $order->order_number,
                status: \App\Enums\PaymentStatus::CANCELLED,
                providerTransactionId: $trxId,
                rawPayload: ['order_id' => $order->order_number, 'transaction_status' => 'cancel']
            );
            $this->cancelPendingOrder();
            $this->feedbackMessage = "⚡ [DEV SIMULATOR] Webhook 'cancel' diproses. Order {$order->order_number} ditandai CANCELLED.";
        }
    }

    /**
     * Test Idempotency: Trigger duplicate settlement webhook to verify no double deduction
     */
    public function simulateDuplicateWebhookTest(OrderService $orderService): void
    {
        $order = null;
        if ($this->lastCompletedOrderId) {
            $order = Order::find($this->lastCompletedOrderId);
        } else {
            $order = Order::where('status', \App\Enums\OrderStatus::PAID)->latest()->first();
        }

        if (!$order) {
            $this->feedbackMessage = '[DEV TOOLBAR] Selesaikan minimal 1 order lunas untuk menguji webhook duplikat.';
            return;
        }

        // Fire duplicate notification
        $orderService->settlePaymentFromWebhook(
            orderNumber: $order->order_number,
            providerTransactionId: 'TEST-DUP-' . strtoupper(Str::random(6)),
            paymentType: 'qris',
            rawPayload: ['duplicate_test' => true, 'timestamp' => now()->toIso8601String()]
        );

        $this->feedbackMessage = "⚡ [IDEMPOTENCY VERIFIED] Webhook duplikat untuk {$order->order_number} berhasil diabaikan! Stok aman tanpa double deduction.";
    }

    protected function completeOnlineOrderReceipt(string $orderNumber, string $paymentLabel): void
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->first();

        if ($order) {
            $this->lastReceiptData = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'created_at' => $order->created_at->format('d M Y · H:i') . ' WIB',
                'items' => $order->items->map(fn($it) => [
                    'name' => $it->product_name,
                    'price' => $it->unit_price,
                    'quantity' => $it->quantity,
                ])->toArray(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'tax_amount' => $order->tax_amount,
                'total_amount' => $order->total_amount,
                'cash_tendered' => $order->total_amount,
                'change_amount' => 0,
                'payment_method' => $paymentLabel,
                'cashier_name' => auth()->user()->name ?? 'Zerotsu Cashier',
            ];

            $this->lastCompletedOrderId = $order->id;
            $this->clearCart();
            $this->showSnapWaitingModal = false;
            $this->showSnapSimulatorModal = false;
            $this->showReceiptModal = true;
            $this->pendingOrderNumber = null;
            $this->pendingSnapToken = null;
        }
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->lastReceiptData = [];
    }

    // ══════════════════════════════════════════════════════════
    // COMPUTED FINANCIAL PROPERTIES
    // ══════════════════════════════════════════════════════════

    #[Computed]
    public function subtotal(): int
    {
        $subtotal = 0;
        foreach ($this->cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }
        return $subtotal;
    }

    #[Computed]
    public function discountAmount(): int
    {
        $subtotal = $this->subtotal;
        if ($this->discountType === 'percent') {
            $percent = min(100, max(0, $this->discountValue));
            $amount = (int) round($subtotal * ($percent / 100));
        } else {
            $amount = $this->discountValue;
        }
        return min($subtotal, max(0, $amount));
    }

    #[Computed]
    public function taxAmount(): int
    {
        if (!$this->enableTax) {
            return 0;
        }
        $taxable = max(0, $this->subtotal - $this->discountAmount);
        return (int) round($taxable * ($this->taxRate / 100));
    }

    #[Computed]
    public function grandTotal(): int
    {
        return max(0, $this->subtotal - $this->discountAmount + $this->taxAmount);
    }

    #[Computed]
    public function totalUnits(): int
    {
        $units = 0;
        foreach ($this->cart as $item) {
            $units += $item['quantity'];
        }
        return $units;
    }

    #[Computed]
    public function changeAmount(): int
    {
        return max(0, $this->cashTendered - $this->grandTotal);
    }

    #[Computed]
    public function isCashSufficient(): bool
    {
        return $this->cashTendered >= $this->grandTotal;
    }

    public function render()
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $totalActiveProducts = Product::where('is_active', true)->count();

        $products = Product::with('category')
            ->where('is_active', true)
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->when(trim($this->search), function ($query) {
                $term = trim($this->search);
                $query->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('livewire.pos-terminal', [
            'categories' => $categories,
            'products' => $products,
            'totalActiveProducts' => $totalActiveProducts,
            'activeCategoryName' => $this->selectedCategory
                ? $categories->firstWhere('id', $this->selectedCategory)?->name
                : 'All Items',
        ]);
    }
}
