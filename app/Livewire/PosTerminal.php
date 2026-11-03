<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
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

    // Cart State (Wireframe initialized for Phase 3.3 integration)
    public array $cart = [];
    public ?string $feedbackMessage = null;

    public function selectCategory(?int $categoryId = null): void
    {
        $this->selectedCategory = $categoryId;
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

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
            $this->feedbackMessage = "Batas stok tercapai untuk [{$product->name}] (Max: {$product->stock}).";
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
