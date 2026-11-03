<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General Hardware',
                'slug' => 'general-hardware',
                'description' => 'Standard hardware tools and foundational equipment.',
                'image' => null,
            ],
            [
                'name' => 'Modular Systems',
                'slug' => 'modular-systems',
                'description' => 'Configurable modules and architectural components.',
                'image' => null,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Everyday add-ons, attachments, and utility kits.',
                'image' => null,
            ],
            [
                'name' => 'Custom Solutions',
                'slug' => 'custom-solutions',
                'description' => 'Specialized custom fabrications and prototypes.',
                'image' => null,
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
