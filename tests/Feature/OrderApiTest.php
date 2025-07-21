<?php

namespace Tests\Feature;

use App\Actions\CreateOrderAction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_access_orders()
    {
        $response = $this->getJson('/api/orders')
            ->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_access_orders()
    {
        $user = User::find(1);
        $token = auth()->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/orders')
            ->assertStatus(200);
    }

    #[Test]
    public function  authenticated_user_can_show_single_order()
    {
        $this->withoutExceptionHandling();

        $user = User::has('orders')->with('orders')->First();
        $token = auth()->login($user);

        $order = $user->orders[0];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'comment',
                    'total_amount',
                    'is_completed',
                    'created_at',
                    'updated_at',
                    'products',
                    "links" => ["self"]
                ]
            ])
            ->assertJsonFragment(['id' => $order->id]);
    }

    #[Test]
    public function it_creates_order_with_products()
    {
        $user = User::find(1);
        $token = auth()->login($user);

        $product = Product::active()->First();

        $order = $this->app->make(CreateOrderAction::class)->apply([
            'user_id' => $user->id,
            'products' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'total_amount' => $product->price * 2
        ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'total' => $product->price * 2
        ]);
    }
}
