<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_attempts_are_throttled(): void
    {
        $email = 'wrong@example.com';

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/admin/login', [
                'email' => $email,
                'password' => 'wrong-password',
            ]);
            $response->assertStatus(302); // Redirect back with error
        }

        // 6th attempt should be throttled
        $response = $this->post('/admin/login', [
            'email' => $email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    public function test_admin_can_logout_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_logout_redirects_to_home_on_csrf_mismatch(): void
    {
        // We simulate a logout request without a CSRF token (or invalid one)
        // by manually disabling middleware for this test or using withoutMiddleware,
        // but better is to test the actual handling.
        // Since we added the handler in bootstrap/app.php, a post without session/token should redirect.

        $response = $this->post('/admin/logout'); // Should trigger TokenMismatchException if session is invalid

        $response->assertRedirect('/');
    }
}
