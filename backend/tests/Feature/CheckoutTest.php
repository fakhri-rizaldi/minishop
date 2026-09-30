<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_successful_creates_order_and_reduces_stock(): void
    {
        $category = Category::factory()->create();
        $p1 = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Monstera Deliciosa',
            'price' => 150000,
            'stock' => 10,
        ]);
        $p2 = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Pot Terakota',
            'price' => 50000,
            'stock' => 5,
        ]);

        $payload = [
            'customer' => [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
            ],
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 2],
                ['product_id' => $p2->id, 'quantity' => 1],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'order_number',
                    'customer_name',
                    'customer_email',
                    'total',
                    'created_at',
                    'items' => [
                        '*' => ['product_id', 'product_name', 'unit_price', 'quantity', 'subtotal'],
                    ],
                ],
            ]);

        // Expected total: (150000 * 2) + (50000 * 1) = 350000
        $this->assertEquals(350000, $response->json('data.total'));
        $this->assertEquals('Budi Santoso', $response->json('data.customer_name'));

        // Verify stock decreased in DB
        $this->assertEquals(8, $p1->fresh()->stock);
        $this->assertEquals(4, $p2->fresh()->stock);

        // Verify Order in DB
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi Santoso',
            'total' => 350000,
        ]);

        // Verify Order Items in DB (with snapshot)
        $this->assertDatabaseHas('order_items', [
            'product_id' => $p1->id,
            'product_name' => 'Monstera Deliciosa',
            'unit_price' => 150000,
            'quantity' => 2,
            'subtotal' => 300000,
        ]);
    }

    public function test_checkout_fails_with_409_when_stock_insufficient(): void
    {
        $category = Category::factory()->create();
        $p1 = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Tanaman Langka',
            'price' => 200000,
            'stock' => 2,
        ]);

        $payload = [
            'customer' => [
                'name' => 'Siti',
                'email' => 'siti@example.com',
            ],
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 5],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(409)
            ->assertJson([
                'code' => 'INSUFFICIENT_STOCK',
                'message' => 'Stok sebagian produk tidak mencukupi.',
                'items' => [
                    [
                        'product_id' => $p1->id,
                        'name' => 'Tanaman Langka',
                        'requested' => 5,
                        'available' => 2,
                    ],
                ],
            ]);

        // Stock should remain unchanged
        $this->assertEquals(2, $p1->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_validation_errors(): void
    {
        // 1. Empty body
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer', 'items']);

        // 2. Duplicate product_id in items
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $payloadDuplicate = [
            'customer' => ['name' => 'Andi', 'email' => 'andi@example.com'],
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ];
        $responseDup = $this->postJson('/api/orders', $payloadDuplicate);
        $responseDup->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.product_id', 'items.1.product_id']);

        // 3. Soft deleted product
        $deletedProduct = Product::factory()->create(['category_id' => $category->id]);
        $deletedProduct->delete();

        $payloadDeleted = [
            'customer' => ['name' => 'Andi', 'email' => 'andi@example.com'],
            'items' => [
                ['product_id' => $deletedProduct->id, 'quantity' => 1],
            ],
        ];
        $responseDel = $this->postJson('/api/orders', $payloadDeleted);
        $responseDel->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.product_id']);
    }

    public function test_checkout_rolls_back_completely_if_second_item_fails(): void
    {
        $category = Category::factory()->create();
        $p1 = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produk A Cukup',
            'price' => 100000,
            'stock' => 10,
        ]);
        $p2 = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produk B Habis',
            'price' => 200000,
            'stock' => 1,
        ]);

        $payload = [
            'customer' => ['name' => 'Budi', 'email' => 'budi@example.com'],
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 2], // Cukup
                ['product_id' => $p2->id, 'quantity' => 5], // Kurang (memicu rollback)
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(409);

        // Verify full rollback: p1 stock is still 10 (not 8)
        $this->assertEquals(10, $p1->fresh()->stock);
        $this->assertEquals(1, $p2->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_get_order_summary_success_and_404(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'price' => 100000]);

        $order = Order::create([
            'order_number' => 'MS-260930-ABC123',
            'customer_name' => 'Rina',
            'customer_email' => 'rina@example.com',
            'total' => 100000,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        // Success
        $response = $this->getJson('/api/orders/MS-260930-ABC123');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'order_number' => 'MS-260930-ABC123',
                    'customer_name' => 'Rina',
                    'total' => 100000,
                ],
            ]);

        // 404 Not Found
        $response404 = $this->getJson('/api/orders/MS-NOTEXISTING');
        $response404->assertStatus(404)
            ->assertJson([
                'message' => 'Data tidak ditemukan.',
            ]);
    }
}
