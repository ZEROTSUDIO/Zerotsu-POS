<?php

namespace Tests\Feature;

use App\Livewire\Catalogues;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_unique_slug_for_new_products()
    {
        $category = Category::factory()->create();

        // Create first product
        Livewire::test(Catalogues::class)
            ->set('code', 'P001')
            ->set('name', 'Classic Bench')
            ->set('description', 'A beautiful classic bench for your garden.')
            ->set('category_id', $category->id)
            ->call('createCatalogue')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Classic Bench',
            'slug' => 'classic-bench',
        ]);

        // Create second product with same name
        Livewire::test(Catalogues::class)
            ->set('code', 'P002')
            ->set('name', 'Classic Bench')
            ->set('description', 'Another classic bench for your garden.')
            ->set('category_id', $category->id)
            ->call('createCatalogue')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Classic Bench',
            'slug' => 'classic-bench-2',
        ]);
    }

    public function test_it_does_not_change_slug_on_update_if_name_is_same()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-name',
            'category_id' => $category->id,
        ]);

        Livewire::test(Catalogues::class)
            ->call('editCatalogue', $product->id)
            ->set('name', 'Original Name')
            ->call('updateCatalogue')
            ->assertHasNoErrors();

        $product->refresh();
        $this->assertEquals('original-name', $product->slug);
    }

    public function test_it_updates_slug_uniquely_on_name_change()
    {
        $category = Category::factory()->create();

        // Create an existing product with the target name
        Product::factory()->create([
            'name' => 'Target Name',
            'slug' => 'target-name',
            'category_id' => $category->id,
        ]);

        // Create another product to edit
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-name',
            'category_id' => $category->id,
        ]);

        Livewire::test(Catalogues::class)
            ->call('editCatalogue', $product->id)
            ->set('name', 'Target Name')
            ->call('updateCatalogue')
            ->assertHasNoErrors();

        $product->refresh();
        $this->assertEquals('target-name-2', $product->slug);
    }
}
