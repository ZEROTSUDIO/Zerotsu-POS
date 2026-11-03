<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Catalogues;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CataloguesImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_catalogue_with_image_processing()
    {
        Storage::fake('public_direct');
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();

        $file = UploadedFile::fake()->image('test-image.jpg', 1500, 1500);

        Livewire::test(Catalogues::class)
            ->set('code', 'PRD-001')
            ->set('name', 'Test Product')
            ->set('description', 'This is a test product description with enough length.')
            ->set('category_id', $category->id)
            ->set('image', $file)
            ->call('createCatalogue')
            ->assertStatus(200)
            ->assertHasNoErrors();

        // Verify image exists on disk
        $files = Storage::disk('public_direct')->allFiles('products');
        $this->assertCount(1, $files);
    }
}
