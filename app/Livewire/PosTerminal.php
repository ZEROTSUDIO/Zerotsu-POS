<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
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
    
    // UI Helpers
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
