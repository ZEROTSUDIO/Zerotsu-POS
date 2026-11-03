<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Catalogues;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CataloguesDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_duplicate_catalogue(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        $original = Product::factory()->create([
            'code' => 'PRD-001',
            'name' => 'Test Product',
            'category_id' => $category->id,
            'description' => 'A detailed product description here.',
            'specification' => ['Color' => 'Gray', 'Size' => 'Large'],
        ]);

        Livewire::test(Catalogues::class)
            ->call('duplicateCatalogue', $original->id)
            ->assertHasNoErrors();

        $this->assertDatabaseCount('products', 2);

        $duplicate = Product::where('id', '!=', $original->id)->first();
        $this->assertEquals('PRD-001-COPY', $duplicate->code);
        $this->assertEquals($original->name, $duplicate->name);
        $this->assertEquals($original->category_id, $duplicate->category_id);
        $this->assertEquals($original->description, $duplicate->description);
        $this->assertEquals($original->specification, $duplicate->specification);
        $this->assertEmpty($duplicate->is_featured);
        $this->assertNotEquals($original->id, $duplicate->id);
    }

    public function test_duplicate_copies_images(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        $original = Product::factory()->create([
            'code' => 'PRD-IMG',
            'category_id' => $category->id,
            'image' => 'products/test-image.jpg',
        ]);

        ProductImage::create([
            'product_id' => $original->id,
            'image_path' => 'products/test-image.jpg',
            'sort_order' => 0,
        ]);

        ProductImage::create([
            'product_id' => $original->id,
            'image_path' => 'products/test-image-2.jpg',
            'sort_order' => 1,
        ]);

        Livewire::test(Catalogues::class)
            ->call('duplicateCatalogue', $original->id)
            ->assertHasNoErrors();

        $duplicate = Product::where('id', '!=', $original->id)->first();

        $this->assertEquals(2, $duplicate->images()->count());
        $this->assertEquals('products/test-image.jpg', $duplicate->image);
        $this->assertEquals(
            $original->images()->orderBy('sort_order')->pluck('image_path')->toArray(),
            $duplicate->images()->orderBy('sort_order')->pluck('image_path')->toArray()
        );
    }

    public function test_duplicate_generates_unique_code(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        $original = Product::factory()->create([
            'code' => 'PRD-002',
            'category_id' => $category->id,
        ]);

        // First duplicate
        Livewire::test(Catalogues::class)
            ->call('duplicateCatalogue', $original->id);

        // Second duplicate
        Livewire::test(Catalogues::class)
            ->call('duplicateCatalogue', $original->id);

        $this->assertDatabaseCount('products', 3);
        $this->assertDatabaseHas('products', ['code' => 'PRD-002-COPY']);
        $this->assertDatabaseHas('products', ['code' => 'PRD-002-COPY-2']);
    }
}
