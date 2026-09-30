<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    /**
     * Store a newly created order (Checkout).
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->checkoutService->place(
            $request->input('customer'),
            $request->input('items')
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified order summary by order number.
     */
    public function show(string $orderNumber): OrderResource
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return new OrderResource($order);
    }
}
