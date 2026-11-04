<?php

namespace App\Livewire;

use App\Enums\PaymentMethod;
use App\Models\Category;
use App\Models\Product;
use App\Services\OrderService;
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
    
    // Phase 3.4 Checkout Dossier State
    public bool $showCheckoutModal = false;
    public string $selectedPaymentMethod = 'cash'; // 'cash' | 'midtrans_snap'
    public int $cashTendered = 0;
    public bool $showReceiptModal = false;
    public ?int $lastCompletedOrderId = null;
    public array $lastReceiptData = [];

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
    // PHASE 3.4 CHECKOUT & CASH RECONCILIATION METHODS
    // ══════════════════════════════════════════════════════════

    #[On('trigger-checkout')]
    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) {
            $this->feedbackMessage = 'Keranjang kosong. Pilih item menu terlebih dahulu!';
            return;
        }

        $this->selectedPaymentMethod = 'cash';
        $this->cashTendered = $this->grandTotal; // default to exact amount due
        $this->showCheckoutModal = true;
        $this->feedbackMessage = null;
    }

    #[On('close-modals')]
    public function closeCheckoutModal(): void
    {
        $this->showCheckoutModal = false;
        $this->showReceiptModal = false;
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
