<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing products
        $products = Product::all();

        // Add 2-4 additional images for each product
        foreach ($products as $product) {
            $imageCount = rand(2, 4);
            for ($i = 0; $i < $imageCount; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'products/sample-' . $product->id . '-' . ($i + 1) . '.jpg',
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
