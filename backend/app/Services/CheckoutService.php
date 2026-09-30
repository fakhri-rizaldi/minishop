<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Place an order inside a database transaction with pessimistic locking.
     *
     * @param  array{name: string, email: string}  $customer
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     *
     * @throws InsufficientStockException
     */
    public function place(array $customer, array $items): Order
    {
        return DB::transaction(function () use ($customer, $items) {
            // Sort items by product_id ascending to prevent deadlock across concurrent checkout requests
            $sortedItems = collect($items)->sortBy('product_id')->values()->all();
            $productIds = array_column($sortedItems, 'product_id');

            // Lock products rows with FOR UPDATE in ordered sequence
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $insufficientItems = [];
            foreach ($sortedItems as $item) {
                /** @var Product|null $product */
                $product = $products->get($item['product_id']);
                $available = $product && ! $product->trashed() ? $product->stock : 0;
                $productName = $product ? $product->name : 'Produk tidak tersedia';

                if ($available < $item['quantity']) {
                    $insufficientItems[] = [
                        'product_id' => $item['product_id'],
                        'name' => $productName,
                        'requested' => (int) $item['quantity'],
                        'available' => (int) $available,
                    ];
                }
            }

            if (! empty($insufficientItems)) {
                throw new InsufficientStockException($insufficientItems);
            }

            // Create Order record (initial total 0)
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'],
                'total' => 0,
            ]);

            $total = 0;
            foreach ($sortedItems as $item) {
                /** @var Product $product */
                $product = $products->get($item['product_id']);
                $subtotal = $product->price * $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);

                // Decrement stock in database
                $product->decrement('stock', $item['quantity']);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            return $order->load('items');
        }, 3);
    }
}
