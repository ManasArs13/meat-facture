<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Товары"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get filtered and sorted list of products",
     *     operationId="getProducts",
     *     @OA\Parameter(
     *         name="price_from",
     *         in="query",
     *         description="Minimum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=100.50)
     *     ),
     *     @OA\Parameter(
     *         name="price_to",
     *         in="query",
     *         description="Maximum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=500.00)
     *     ),
     *     @OA\Parameter(
     *         name="is_available",
     *         in="query",
     *         description="Filter by available status",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by (id, name, price, created_at)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"id", "name", "price", "created_at"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction (asc, desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Meat"),
     *                 @OA\Property(property="description", type="string", example="text"),
     *                 @OA\Property(property="category", type="string", example="Meat"),
     *                 @OA\Property(property="price", type="integer", example=100),
     *                 @OA\Property(property="is_available", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *             )
     *         )
     *     )
     * )
     */
    public function index(ProductRequest $request): ProductCollection
    {
        $products = Product::query()
            ->filter($request->validated())
            ->sort($request->sort_by, $request->sort_dir)
            ->get();

        return new ProductCollection($products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Get product by ID",
     *     operationId="getProductById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Meat"),
     *                 @OA\Property(property="description", type="string", example="text"),
     *                 @OA\Property(property="category", type="string", example="Meat"),
     *                 @OA\Property(property="price", type="integer", example=100),
     *                 @OA\Property(property="is_available", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }
}
