<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            TopicSeeder::class,
            AdminUserSeeder::class,
        ]);

        $author = User::first();
        $topicNews = Topic::where('slug', 'company-news')->first();
        $topicGuide = Topic::where('slug', 'guides-and-tutorials')->first();
        $topicUpdate = Topic::where('slug', 'product-updates')->first();

        $articles = [
            [
                'title' => 'Welcome to the New Starter Platform',
                'slug' => 'welcome-to-the-new-starter-platform',
                'topic_id' => $topicNews ? $topicNews->id : 1,
                'author_id' => $author ? $author->id : 1,
                'image' => null,
                'body' => '<p>Welcome to our new application portal. This boilerplate is built with modern Laravel, Livewire, and Tailwind CSS to help teams build fast, clean, and scalable web solutions.</p><p>Explore the catalog or check back regularly for updates and industry insights.</p>',
                'status' => ArticleStatus::Published,
            ],
            [
                'title' => 'Modular Architecture: Building for Scalability',
                'slug' => 'modular-architecture-building-for-scalability',
                'topic_id' => $topicGuide ? $topicGuide->id : 1,
                'author_id' => $author ? $author->id : 1,
                'image' => null,
                'body' => '<p>Modular systems allow engineering teams to swap, upgrade, and iterate on components without re-architecting the entire codebase.</p><p>In this guide, we explore how decoupled components and reusable service layers streamline production velocity.</p>',
                'status' => ArticleStatus::Published,
            ],
            [
                'title' => 'Q4 Engineering and Product Release Notes',
                'slug' => 'q4-engineering-and-product-release-notes',
                'topic_id' => $topicUpdate ? $topicUpdate->id : 1,
                'author_id' => $author ? $author->id : 1,
                'image' => null,
                'body' => '<p>Our engineering team has rolled out new enhancements across our catalog and management dashboards, improving response times and interface responsiveness.</p>',
                'status' => ArticleStatus::Published,
            ],
        ];

        foreach ($articles as $art) {
            Article::firstOrCreate(['slug' => $art['slug']], $art);
        }
    }
}
