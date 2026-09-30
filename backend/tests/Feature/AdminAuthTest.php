<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_successful_returns_token_and_user(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@minishop.test',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@minishop.test',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);

        $this->assertEquals('admin@minishop.test', $response->json('data.user.email'));
    }

    public function test_admin_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@minishop.test',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@minishop.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_admin_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/logout');
        $response->assertStatus(204);

        $this->assertCount(0, $user->tokens);
    }

    public function test_unauthenticated_access_to_admin_routes_returns_401(): void
    {
        $response = $this->getJson('/api/admin/products');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
