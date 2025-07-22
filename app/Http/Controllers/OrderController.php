<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderCreatedResource;
use App\Models\Order;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Заказы"
 * )
 */
class OrderController extends Controller
{
    public function __construct(
        private CreateOrderAction $createOrder,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Get filtered and sorted list of orders",
     *     operationId="getOrders",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="total_amount_from",
     *         in="query",
     *         description="Minimum total amount filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=1.00)
     *     ),
     *     @OA\Parameter(
     *         name="total_amount_to",
     *         in="query",
     *         description="Maximum total amount filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=100000.00)
     *     ),
     *     @OA\Parameter(
     *         name="is_completed",
     *         in="query",
     *         description="Filter by completion status",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by (name, is_completed, total_amount, created_at)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "is_completed", "total_amount", "created_at"})
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
     *                 @OA\Property(property="comment", type="string", example="text"),
     *                 @OA\Property(property="total_amount", type="integer", example=100),
     *                 @OA\Property(property="is_completed", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(
     *                      property="products",
     *                      type="array",
     *                          @OA\Items(
     *                          type="object",
     *                          required={"product_id", "quantity"},
     *                          @OA\Property(property="product_id", type="integer", example=1),
     *                          @OA\Property(property="quantity", type="integer", minimum=1, example=2)
     *                          )
     *                  ),
     *             )
     *         )
     *     )
     * )
     */
    public function index(OrderRequest $request): OrderCollection
    {
        $orders = Order::query()
            ->filter($request->validated())
            ->sort($request->sort_by, $request->sort_dir)
            ->get();

        return new OrderCollection($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get order by ID",
     *     operationId="geOrderById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *                 @OA\Property(property="comment", type="string", example="text"),
     *                 @OA\Property(property="total_amount", type="integer", example=100),
     *                 @OA\Property(property="is_completed", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(
     *                      property="products",
     *                      type="array",
     *                          @OA\Items(
     *                          type="object",
     *                          required={"product_id", "quantity"},
     *                          @OA\Property(property="product_id", type="integer", example=1),
     *                          @OA\Property(property="quantity", type="integer", minimum=1, example=2)
     *                          )
     *                  ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     )
     * )
     */
    public function show(Order $order): OrderResource
    {
        return new OrderResource($order);
    }

    /**
     * Create new order
     *
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create new order",
     *     operationId="createOrder",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Order data",
     *         @OA\JsonContent(
     *             required={"products"},
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", minimum=1, example=2)
     *                 )
     *             ),
     *             @OA\Property(property="comment", type="string", maxLength=500, example="Please deliver after 5pm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=99.98),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="products.0.product_id", type="array",
     *                     @OA\Items(type="string", example="The selected products.0.product_id is invalid.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function store(OrderStoreRequest $request)
    {
        try {
            $order = $this->createOrder->apply($request->validated());

            return new OrderCreatedResource($order);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
