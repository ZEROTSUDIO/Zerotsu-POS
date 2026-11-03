<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Product;
use App\Models\Category;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $routes = [
            '/',
            '/catalogs',
            '/contact',
            '/profile',
        ];

        foreach ($routes as $route) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . url($route) . '</loc>';
            $sitemap .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.8</priority>';
            $sitemap .= '</url>';
        }

        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . url('/catalogs?category=' . $category->slug) . '</loc>';
            $sitemap .= '<lastmod>' . $category->updated_at->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.7</priority>';
            $sitemap .= '</url>';
        }

        // Products (Catalogues)
        $products = Product::with('category')->get();
        foreach ($products as $product) {
            if ($product->category) {
                $url = url('/catalogs/' . $product->category->slug . '/' . $product->slug);
            } else {
                $url = url('/catalogs/uncategorized/' . $product->slug);
            }

            $sitemap .= '<url>';
            $sitemap .= '<loc>' . $url . '</loc>';
            $sitemap .= '<lastmod>' . $product->updated_at->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.9</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';

        File::put(public_path('sitemap.xml'), $sitemap);

        $this->info('Sitemap generated successfully at public/sitemap.xml');
    }
}
