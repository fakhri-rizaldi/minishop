<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsufficientStockException extends Exception
{
    /**
     * @var array<int, array{product_id: int, name: string, requested: int, available: int}>
     */
    protected array $items;

    /**
     * @param  array<int, array{product_id: int, name: string, requested: int, available: int}>  $items
     */
    public function __construct(array $items, string $message = 'Stok sebagian produk tidak mencukupi.')
    {
        parent::__construct($message, 409);
        $this->items = $items;
    }

    /**
     * Get the insufficient items details.
     *
     * @return array<int, array{product_id: int, name: string, requested: int, available: int}>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'code' => 'INSUFFICIENT_STOCK',
            'items' => $this->items,
        ], 409);
    }
}
