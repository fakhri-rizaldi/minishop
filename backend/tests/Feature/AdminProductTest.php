<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create();
        Sanctum::actingAs($admin);
    }

    public function test_admin_can_list_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/admin/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'stock', 'category'],
                ],
                'meta' => ['current_page', 'total'],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_admin_can_create_product(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Monstera Varigata',
            'description' => 'Tanaman hias langka dengan corak daun putih.',
            'price' => 500000,
            'stock' => 5,
            'image_url' => 'https://picsum.photos/seed/monstera-var/600/600',
        ];

        $response = $this->postJson('/api/admin/products', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Monstera Varigata',
                    'price' => 500000,
                    'stock' => 5,
                    'category' => [
                        'id' => $category->id,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Monstera Varigata',
            'price' => 500000,
        ]);
    }

    public function test_admin_create_product_validation_errors(): void
    {
        $response = $this->postJson('/api/admin/products', [
            'category_id' => 9999, // Non-existent category
            'name' => '',
            'price' => -100,
            'stock' => -5,
            'image_url' => 'not-a-valid-url',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id', 'name', 'price', 'stock', 'image_url']);
    }

    public function test_admin_can_update_product(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produk Lama',
            'price' => 100000,
            'stock' => 10,
        ]);

        $payload = [
            'category_id' => $category->id,
            'name' => 'Produk Diperbarui',
            'description' => 'Deskripsi baru.',
            'price' => 125000,
            'stock' => 8,
            'image_url' => null,
        ];

        $response = $this->putJson('/api/admin/products/'.$product->id, $payload);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => 'Produk Diperbarui',
                    'price' => 125000,
                    'stock' => 8,
                ],
            ]);

        $this->assertEquals('Produk Diperbarui', $product->fresh()->name);
    }

    public function test_admin_can_soft_delete_product_without_breaking_orders(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Tanaman Bonsai',
            'price' => 300000,
        ]);

        $order = Order::factory()->create();
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Tanaman Bonsai',
            'unit_price' => 300000,
            'quantity' => 1,
            'subtotal' => 300000,
        ]);

        // Soft delete product
        $response = $this->deleteJson('/api/admin/products/'.$product->id);
        $response->assertStatus(204);

        // Product is soft deleted
        $this->assertSoftDeleted('products', ['id' => $product->id]);

        // Order and item remain completely intact
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertDatabaseHas('order_items', ['id' => $orderItem->id]);

        // Trashed relation works
        $this->assertEquals('Tanaman Bonsai', $orderItem->fresh()->product->name);
    }
}
