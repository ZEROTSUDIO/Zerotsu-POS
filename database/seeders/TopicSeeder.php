<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            ['name' => 'Company News', 'slug' => 'company-news'],
            ['name' => 'Guides & Tutorials', 'slug' => 'guides-and-tutorials'],
            ['name' => 'Product Updates', 'slug' => 'product-updates'],
            ['name' => 'Industry Insights', 'slug' => 'industry-insights'],
        ];

        foreach ($topics as $t) {
            Topic::firstOrCreate(['slug' => $t['slug']], $t);
        }
    }
}
