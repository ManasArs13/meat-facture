<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    public function apply(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $order = Order::create([
                'user_id' => $data['user_id'],
                'comment' => $data['comment'] ?? null,
                'total_amount' => 0
            ]);

            $totalAmount = 0;
            
            foreach ($data['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $this->validateAvailableProduct($product, $item['quantity']);
                
                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;
                
                $order->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal
                ]);
            }
            
            $order->update(['total_amount' => $totalAmount]);
            
            return $order;
        });
    }
    
    protected function validateAvailableProduct(Product $product): void
    {
        if (!$product->is_available) {
            throw ValidationException::withMessages([
                'products' => "Product {$product->id} is not available"
            ]);
        }
    }
}