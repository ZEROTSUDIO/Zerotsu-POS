<?php

namespace Database\Seeders;

use App\Enums\InventoryMovementType;
use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ZerotsuCafeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Cashier / Admin User
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@zerotsu.test'],
            [
                'name' => 'Zerotsu Cashier',
                'username' => 'cashier',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Categories
        $categoriesData = [
            ['name' => 'Kopi', 'slug' => 'kopi', 'description' => 'Varian espresso based dan signature coffee'],
            ['name' => 'Non-Kopi', 'slug' => 'non-kopi', 'description' => 'Minuman artisan non-coffee'],
            ['name' => 'Snack & Pastry', 'slug' => 'snack-pastry', 'description' => 'Camilan dan makanan pendamping santai'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // 3. Products
        $productsData = [
            // Kopi
            [
                'category' => 'kopi',
                'sku' => 'KOP-AREN',
                'name' => 'Kopi Susu Gula Aren',
                'cost_price' => 8000,
                'selling_price' => 18000,
                'stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'cup',
            ],
            [
                'category' => 'kopi',
                'sku' => 'KOP-AMER',
                'name' => 'Iced Americano',
                'cost_price' => 5000,
                'selling_price' => 15000,
                'stock' => 45,
                'minimum_stock' => 10,
                'unit' => 'cup',
            ],
            [
                'category' => 'kopi',
                'sku' => 'KOP-CARM',
                'name' => 'Caramel Macchiato',
                'cost_price' => 11000,
                'selling_price' => 24000,
                'stock' => 30,
                'minimum_stock' => 5,
                'unit' => 'cup',
            ],

            // Non-Kopi
            [
                'category' => 'non-kopi',
                'sku' => 'NON-MATC',
                'name' => 'Kyoto Matcha Latte',
                'cost_price' => 10000,
                'selling_price' => 22000,
                'stock' => 40,
                'minimum_stock' => 8,
                'unit' => 'cup',
            ],
            [
                'category' => 'non-kopi',
                'sku' => 'NON-REDV',
                'name' => 'Velvet Cream Latte',
                'cost_price' => 9500,
                'selling_price' => 20000,
                'stock' => 35,
                'minimum_stock' => 5,
                'unit' => 'cup',
            ],
            [
                'category' => 'non-kopi',
                'sku' => 'NON-CHOC',
                'name' => 'Signature Dark Chocolate',
                'cost_price' => 9000,
                'selling_price' => 20000,
                'stock' => 40,
                'minimum_stock' => 8,
                'unit' => 'cup',
            ],

            // Snack
            [
                'category' => 'snack-pastry',
                'sku' => 'SNK-FRIE',
                'name' => 'Crispy French Fries (Truffle)',
                'cost_price' => 8500,
                'selling_price' => 18000,
                'stock' => 25,
                'minimum_stock' => 5,
                'unit' => 'porsi',
            ],
            [
                'category' => 'snack-pastry',
                'sku' => 'SNK-NUGG',
                'name' => 'Golden Chicken Nuggets',
                'cost_price' => 10000,
                'selling_price' => 20000,
                'stock' => 30,
                'minimum_stock' => 5,
                'unit' => 'porsi',
            ],
            [
                'category' => 'snack-pastry',
                'sku' => 'SNK-CROI',
                'name' => 'Butter Croissant',
                'cost_price' => 12000,
                'selling_price' => 22000,
                'stock' => 15,
                'minimum_stock' => 3,
                'unit' => 'pcs',
            ],
        ];

        foreach ($productsData as $item) {
            $category = $categories[$item['category']];

            $product = Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                    'stock' => $item['stock'],
                    'minimum_stock' => $item['minimum_stock'],
                    'unit' => $item['unit'],
                    'is_active' => true,
                ]
            );

            // Record initial opening stock movement if not exists
            if (!InventoryMovement::where('product_id', $product->id)->exists()) {
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => InventoryMovementType::OPENING_BALANCE,
                    'quantity' => $item['stock'],
                    'stock_before' => 0,
                    'stock_after' => $item['stock'],
                    'reference_type' => null,
                    'reference_id' => null,
                    'note' => 'Saldo awal pembukaan kafe',
                    'created_by' => $cashier->id,
                ]);
            }
        }
    }
}
