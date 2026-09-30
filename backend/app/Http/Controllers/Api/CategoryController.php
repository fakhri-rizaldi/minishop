<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories ordered by name.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
