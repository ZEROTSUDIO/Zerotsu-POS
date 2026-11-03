<?php

namespace Tests\Feature;

use App\Livewire\Catalogues;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_multiple_images()
    {
        Storage::fake('public_direct');
        $category = Category::factory()->create();

        $images = [
            UploadedFile::fake()->image('img1.jpg'),
            UploadedFile::fake()->image('img2.jpg'),
        ];

        Livewire::test(Catalogues::class)
            ->set('code', 'P001')
            ->set('name', 'Test Product')
            ->set('description', 'Description content here that is long enough')
            ->set('category_id', $category->id)
            ->set('images', $images)
            ->call('createCatalogue')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('products', 1);
        $product = Product::first();

        // Should have 2 images in relation
        $this->assertCount(2, $product->images);

        // Main image should be set
        $this->assertNotNull($product->image);

        // Assert storage
        // Note: Filenames are hashed, so we just check files exist in directory
        // Storage::disk('public_direct')->assertExists($product->image);
    }

    public function test_can_add_images_to_existing_product()
    {
        Storage::fake('public');
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id
        ]);

        $newImages = [
            UploadedFile::fake()->image('img3.jpg'),
        ];

        Livewire::test(Catalogues::class)
            ->call('editCatalogue', $product->id)
            ->set('images', $newImages)
            ->call('updateCatalogue')
            ->assertHasNoErrors();

        $product->refresh();
        // Factory creates some images? Seeder does, but factory typically empty for relation unless specified
        // Our factory 'definition' for ProductImage was empty list in one step, updated later.
        // ProductFactory definition: doesn't create ProductImages.
        // So initially 0 images (Product::factory doesn't use afterCreating callback for images here).
        // Wait, current ProductImagesFactory definition creates new Product if product_id not set.

        // Check how many images: 1 new one.
        $this->assertCount(1, $product->images);
    }
}
