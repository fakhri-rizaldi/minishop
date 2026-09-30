<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_categories_returns_ordered_list(): void
    {
        Category::factory()->create(['name' => 'Tanaman Indoor', 'slug' => 'tanaman-indoor']);
        Category::factory()->create(['name' => 'Dekorasi Rumah', 'slug' => 'dekorasi-rumah']);
        Category::factory()->create(['name' => 'Pot & Wadah', 'slug' => 'pot-dan-wadah']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug'],
                ],
            ]);

        $names = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertEquals(['Dekorasi Rumah', 'Pot & Wadah', 'Tanaman Indoor'], $names);
    }

    public function test_get_products_default_pagination(): void
    {
        $category = Category::factory()->create(['name' => 'Tanaman', 'slug' => 'tanaman']);
        Product::factory()->count(15)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'price', 'stock', 'image_url', 'category'],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        $this->assertCount(12, $response->json('data'));
        $this->assertEquals(15, $response->json('meta.total'));
    }

    public function test_get_products_search_filter_and_wildcard_escaping(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Monstera Deliciosa']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Pupuk Organik 100% Asli']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Kaktus Mini']);

        // Case insensitive search
        $response = $this->getJson('/api/products?search=monstera');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Monstera Deliciosa', $response->json('data.0.name'));

        // Wildcard character % search should only match the product containing %
        $responseWildcard = $this->getJson('/api/products?search=%');
        $responseWildcard->assertStatus(200);
        $this->assertCount(1, $responseWildcard->json('data'));
        $this->assertEquals('Pupuk Organik 100% Asli', $responseWildcard->json('data.0.name'));
    }

    public function test_get_products_category_filter(): void
    {
        $cat1 = Category::factory()->create(['slug' => 'tanaman']);
        $cat2 = Category::factory()->create(['slug' => 'pot']);

        Product::factory()->create(['category_id' => $cat1->id, 'name' => 'Monstera']);
        Product::factory()->create(['category_id' => $cat2->id, 'name' => 'Pot Terakota']);

        $response = $this->getJson('/api/products?category=tanaman');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Monstera', $response->json('data.0.name'));

        // Non-existent category returns empty array
        $responseEmpty = $this->getJson('/api/products?category=kategori-palsu');
        $responseEmpty->assertStatus(200);
        $this->assertCount(0, $responseEmpty->json('data'));
    }

    public function test_get_products_per_page_bounds(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(60)->create(['category_id' => $category->id]);

        // Max per_page is 50
        $response = $this->getJson('/api/products?per_page=100');
        $response->assertStatus(200);
        $this->assertCount(50, $response->json('data'));
        $this->assertEquals(50, $response->json('meta.per_page'));

        // Invalid per_page falls back to default 12
        $responseInvalid = $this->getJson('/api/products?per_page=-5');
        $responseInvalid->assertStatus(200);
        $this->assertCount(12, $responseInvalid->json('data'));
    }

    public function test_soft_deleted_products_do_not_appear_in_catalog(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'Bunga Anggrek']);
        $product->delete();

        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    public function test_get_product_detail_success_and_404(): void
    {
        $category = Category::factory()->create(['name' => 'Tanaman']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Sansevieria',
            'price' => 75000,
        ]);

        // Success
        $response = $this->getJson('/api/products/'.$product->id);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => 'Sansevieria',
                    'price' => 75000,
                    'category' => [
                        'id' => $category->id,
                        'name' => 'Tanaman',
                    ],
                ],
            ]);

        // 404 for non-existent id
        $response404 = $this->getJson('/api/products/99999');
        $response404->assertStatus(404)
            ->assertJson([
                'message' => 'Data tidak ditemukan.',
            ]);

        // 404 for soft-deleted product
        $product->delete();
        $responseDeleted = $this->getJson('/api/products/'.$product->id);
        $responseDeleted->assertStatus(404)
            ->assertJson([
                'message' => 'Data tidak ditemukan.',
            ]);
    }
}
