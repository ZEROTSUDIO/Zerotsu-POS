<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Catalogues;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CataloguesSpecificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_reorder_specifications()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        $specifications = [
            ['key' => 'Spec 1', 'value' => 'Val 1'],
            ['key' => 'Spec 2', 'value' => 'Val 2'],
            ['key' => 'Spec 3', 'value' => 'Val 3'],
        ];

        // Test reordering
        Livewire::test(Catalogues::class)
            ->set('specifications', $specifications)
            ->call('reorderSpecifications', [2, 0, 1])
            ->assertSet('specifications', [
                ['key' => 'Spec 3', 'value' => 'Val 3'],
                ['key' => 'Spec 1', 'value' => 'Val 1'],
                ['key' => 'Spec 2', 'value' => 'Val 2'],
            ]);
    }

    public function test_specifications_persist_in_order()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        // Create product with specific order
        Livewire::test(Catalogues::class)
            ->set('code', 'PRD-001')
            ->set('name', 'Test Product')
            ->set('description', 'This is a test product description with enough length.')
            ->set('category_id', $category->id)
            ->set('specifications', [
                ['key' => 'First', 'value' => '1'],
                ['key' => 'Second', 'value' => '2'],
            ])
            ->call('createCatalogue')
            ->assertHasNoErrors();

        $product = Product::first();
        $this->assertEquals(['First' => '1', 'Second' => '2'], $product->specification);

        // Edit and reorder
        Livewire::test(Catalogues::class)
            ->call('editCatalogue', $product->id)
            ->call('reorderSpecifications', [1, 0])
            ->set('name', 'Updated Product') // Name update to trigger re-save
            ->call('updateCatalogue')
            ->assertHasNoErrors();

        $product->refresh();
        // Since it's stored as a JSON array (mapped from keys), the order might depend on how it's saved/mapped.
        // In createCatalogue/updateCatalogue, it does:
        // $specs = collect($this->specifications)
        //     ->filter(fn($spec) => ! empty($spec['key']) && ! empty($spec['value']))
        //     ->mapWithKeys(fn($spec) => [$spec['key'] => $spec['value']])
        //     ->toArray();
        // PHP associative arrays preserve insertion order. Collect mapWithKeys preserves order.

        $this->assertEquals(['Second' => '2', 'First' => '1'], $product->specification);
    }
}
