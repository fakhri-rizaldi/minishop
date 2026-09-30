<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_required_initial_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 4 categories
        $this->assertDatabaseCount('categories', 4);

        // 23 products (minimal 10 produk dummy per REQ-DATA-01)
        $this->assertDatabaseCount('products', 23);

        // At least one product with stock 0
        $this->assertTrue(Product::where('stock', 0)->exists());

        // At least one product with stock <= 5
        $this->assertTrue(Product::where('stock', '<=', 5)->where('stock', '>', 0)->exists());

        // Admin account
        $this->assertDatabaseHas('users', [
            'email' => 'admin@minishop.test',
        ]);

        $admin = User::where('email', 'admin@minishop.test')->first();
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('categories', 4);
        $this->assertDatabaseCount('products', 23);
        $this->assertDatabaseCount('users', 1);
    }
}
