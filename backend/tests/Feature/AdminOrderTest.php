<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create();
        Sanctum::actingAs($admin);
    }

    public function test_admin_can_list_orders_newest_first(): void
    {
        $order1 = Order::factory()->create(['created_at' => now()->subDay()]);
        $order2 = Order::factory()->create(['created_at' => now()]);

        $response = $this->getJson('/api/admin/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['order_number', 'customer_name', 'customer_email', 'total', 'created_at'],
                ],
                'meta' => ['current_page', 'total'],
            ]);

        $this->assertEquals($order2->order_number, $response->json('data.0.order_number'));
        $this->assertEquals($order1->order_number, $response->json('data.1.order_number'));
    }

    public function test_admin_can_view_order_details(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $order = Order::factory()->create([
            'order_number' => 'MS-260930-ADM001',
            'customer_name' => 'Dewi',
            'total' => 200000,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Ficus Lyrata',
            'unit_price' => 200000,
            'quantity' => 1,
            'subtotal' => 200000,
        ]);

        $response = $this->getJson('/api/admin/orders/'.$order->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'order_number' => 'MS-260930-ADM001',
                    'customer_name' => 'Dewi',
                    'total' => 200000,
                    'items' => [
                        [
                            'product_name' => 'Ficus Lyrata',
                            'unit_price' => 200000,
                            'quantity' => 1,
                            'subtotal' => 200000,
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_can_view_order_details_by_order_number(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $order = Order::factory()->create([
            'order_number' => 'MS-260930-BYNUM01',
            'customer_name' => 'Budi',
            'total' => 150000,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Monstera Deliciosa',
            'unit_price' => 150000,
            'quantity' => 1,
            'subtotal' => 150000,
        ]);

        $response = $this->getJson('/api/admin/orders/MS-260930-BYNUM01');

        $response->assertStatus(200)
            ->assertJsonPath('data.order_number', 'MS-260930-BYNUM01')
            ->assertJsonPath('data.total', 150000);
    }
}
