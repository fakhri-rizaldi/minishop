<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for admin (newest first).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = max(1, min($perPage, 50));

        $orders = Order::query()
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order details for admin.
     */
    public function show(string|int $id): OrderResource
    {
        $order = Order::query()
            ->with('items')
            ->where(function ($q) use ($id) {
                if (is_numeric($id)) {
                    $q->where('id', (int) $id)->orWhere('order_number', (string) $id);
                } else {
                    $q->where('order_number', (string) $id);
                }
            })
            ->firstOrFail();

        return new OrderResource($order);
    }
}
