<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderCreatedResource;
use App\Models\Order;


class OrderController extends Controller
{
    public function __construct(
        private CreateOrderAction $createOrder,
    ) {}

    /**
     * Получить список заказов пользователя
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
     * Показать один заказ
     */
    public function show(Order $order): OrderResource
    {
        return new OrderResource($order);
    }

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
