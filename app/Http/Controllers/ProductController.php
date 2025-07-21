<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;

class ProductController extends Controller
{
    /**
     * Получить список товаров с возможностью фильтрации
     */
    public function index(ProductFilterRequest $request): ProductCollection
    {
        $products = Product::query()
            ->filter($request->validated())
            ->sort($request->sort_by, $request->sort_dir)
            ->get();

        return new ProductCollection($products);
    }

    /**
     * Показать один товар
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }
}
