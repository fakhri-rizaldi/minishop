<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 12);
        if ($perPage < 1) {
            $perPage = 12;
        } elseif ($perPage > 50) {
            $perPage = 50;
        }

        $products = Product::query()
            ->with('category')
            ->search($request->query('search') ?? $request->query('q'))
            ->inCategory($request->query('category'))
            ->orderByDesc('id')
            ->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     */
    public function show(string|int $id): ProductResource
    {
        $product = Product::query()
            ->with('category')
            ->findOrFail((int) $id);

        return new ProductResource($product);
    }
}
