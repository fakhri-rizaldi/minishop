<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the products for admin with search, filter, and pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = max(1, min($perPage, 50));

        $products = Product::query()
            ->with('category')
            ->search($request->query('search') ?? $request->query('q'))
            ->inCategory($request->query('category'))
            ->orderByDesc('id')
            ->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified product for admin form.
     */
    public function show(string|int $id): ProductResource
    {
        $product = Product::query()
            ->with('category')
            ->findOrFail((int) $id);

        return new ProductResource($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, string|int $id): ProductResource
    {
        $product = Product::findOrFail((int) $id);
        $product->update($request->validated());

        return new ProductResource($product->load('category'));
    }

    /**
     * Remove the specified product from storage (Soft Delete).
     */
    public function destroy(string|int $id): Response
    {
        $product = Product::findOrFail((int) $id);
        $product->delete();

        return response()->noContent();
    }
}
