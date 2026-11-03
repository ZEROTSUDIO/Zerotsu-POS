<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Topics;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TopicsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function component_can_render_successfully()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Topics::class)
            ->assertStatus(200)
            ->assertSee('topics');
    }

    /** @test */
    public function can_create_new_topic()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Topics::class)
            ->set('name', 'New Test Topic')
            ->call('createtopic')
            ->assertSet('isCreating', false)
            ->assertSee('topic successfully created.');

        $this->assertTrue(Topic::whereName('New Test Topic')->exists());
    }

    /** @test */
    public function can_update_topic()
    {
        $user = User::factory()->create();
        $topic = Topic::create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        Livewire::actingAs($user)
            ->test(Topics::class)
            ->call('edittopic', $topic->id)
            ->set('name', 'Updated Name')
            ->call('updatetopic')
            ->assertSet('isCreating', false)
            ->assertSee('topic successfully updated.');

        $this->assertEquals('Updated Name', $topic->refresh()->name);
    }

    /** @test */
    public function can_delete_topic()
    {
        $user = User::factory()->create();
        $topic = Topic::create([
            'name' => 'Delete Me',
            'slug' => 'delete-me',
        ]);

        Livewire::actingAs($user)
            ->test(Topics::class)
            ->call('deletetopic', $topic->id)
            ->assertSee('topic successfully deleted.');

        $this->assertFalse(Topic::whereId($topic->id)->exists());
    }
}
