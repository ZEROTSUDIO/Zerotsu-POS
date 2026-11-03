<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
        ]);

        $hardware = Category::where('slug', 'general-hardware')->first();
        $systems  = Category::where('slug', 'modular-systems')->first();
        $accessories = Category::where('slug', 'accessories')->first();

        $products = [
            [
                'code' => 'MOD-101',
                'name' => 'Modular Base Unit Alpha',
                'slug' => 'modular-base-unit-alpha',
                'category_id' => $systems ? $systems->id : 1,
                'description' => 'A heavy-duty precision base module designed for flexible workstation mounting.',
                'specification' => [
                    'material' => 'Anodized Aluminum / Steel',
                    'dimensions' => '120 x 80 x 75 cm',
                    'weight' => '24 kg',
                    'finish' => 'Matte Black',
                ],
                'is_featured' => true,
            ],
            [
                'code' => 'HRD-202',
                'name' => 'Industrial Anchor Clamp',
                'slug' => 'industrial-anchor-clamp',
                'category_id' => $hardware ? $hardware->id : 1,
                'description' => 'High-tensile steel mounting clamp suitable for structural fixtures.',
                'specification' => [
                    'material' => 'Galvanized Steel',
                    'max_load' => '350 kg',
                    'thread_size' => 'M12',
                    'corrosion_resistance' => 'Grade 4',
                ],
                'is_featured' => true,
            ],
            [
                'code' => 'ACC-303',
                'name' => 'Universal Cable Conduit Kit',
                'slug' => 'universal-cable-conduit-kit',
                'category_id' => $accessories ? $accessories->id : 1,
                'description' => 'Flexible modular wiring raceway with integrated clip tracks.',
                'specification' => [
                    'length' => '2.5 meters',
                    'capacity' => 'Up to 12 cables',
                    'color' => 'Slate Gray',
                ],
                'is_featured' => true,
            ],
            [
                'code' => 'MOD-104',
                'name' => 'Modular Expansion Rail Beta',
                'slug' => 'modular-expansion-rail-beta',
                'category_id' => $systems ? $systems->id : 1,
                'description' => 'Secondary rail assembly allowing quick accessory expansion.',
                'specification' => [
                    'material' => 'Extruded Aluminum',
                    'length' => '100 cm',
                    'compatibility' => 'MOD-101 Series',
                ],
                'is_featured' => false,
            ],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(['code' => $prod['code']], $prod);
        }
    }
}
