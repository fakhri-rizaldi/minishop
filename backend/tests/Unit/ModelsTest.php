<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_api_tokens_trait(): void
    {
        $user = new User;
        $this->assertTrue(method_exists($user, 'createToken'));
    }

    public function test_category_and_product_relationships(): void
    {
        $category = Category::create([
            'name' => 'Tanaman Indoor',
            'slug' => 'tanaman-indoor',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Monstera King',
            'description' => 'Tanaman hias berdaun lebar.',
            'price' => 150000,
            'stock' => 10,
        ]);

        $this->assertCount(1, $category->products);
        $this->assertEquals('Tanaman Indoor', $product->category->name);
        $this->assertIsInt($product->price);
        $this->assertIsInt($product->stock);
    }

    public function test_product_scopes(): void
    {
        $category1 = Category::create(['name' => 'Tanaman', 'slug' => 'tanaman']);
        $category2 = Category::create(['name' => 'Pot', 'slug' => 'pot']);

        Product::create([
            'category_id' => $category1->id,
            'name' => 'Monstera Deliciosa',
            'price' => 120000,
            'stock' => 5,
        ]);

        Product::create([
            'category_id' => $category2->id,
            'name' => 'Pot Keramik Putih',
            'price' => 45000,
            'stock' => 8,
        ]);

        $this->assertCount(1, Product::search('Monstera')->get());
        $this->assertCount(1, Product::inCategory('pot')->get());
        $this->assertCount(2, Product::search('')->get());
    }

    public function test_order_number_generation_and_order_items(): void
    {
        $orderNumber = Order::generateOrderNumber();
        $this->assertMatchesRegularExpression('/^MS-\d{6}-[A-Z0-9]{6}$/', $orderNumber);

        $category = Category::create(['name' => 'Tanaman', 'slug' => 'tanaman']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sansevieria',
            'price' => 75000,
            'stock' => 4,
        ]);

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => 'Budi',
            'customer_email' => 'budi@example.com',
            'total' => 150000,
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Sansevieria',
            'unit_price' => 75000,
            'quantity' => 2,
            'subtotal' => 150000,
        ]);

        $this->assertCount(1, $order->items);
        $this->assertEquals($order->id, $orderItem->order->id);
        $this->assertEquals($product->id, $orderItem->product->id);

        // Test withTrashed on product
        $product->delete();
        $this->assertNotNull($orderItem->fresh()->product);
        $this->assertEquals('Sansevieria', $orderItem->fresh()->product->name);
    }
}
