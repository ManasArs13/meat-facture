<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Category::factory(5)->create()->each(function ($category) {
            $category->products()->saveMany(
                Product::factory(10)->make()
            );
        });

         $products = Product::active()->limit(50)->get();

        // Создаем 10 пользователей
        User::factory(10)->create()->each(function ($user) use ($products) {
            // Создаем от 1 до 3 заказов для каждого пользователя
            $orders = Order::factory(rand(1, 3))->create([
                'user_id' => $user->id // Явно указываем владельца заказа
            ]);

            // Для каждого заказа добавляем продукты
            $orders->each(function ($order) use ($products) {
                $selectedProducts = $products->random(rand(1, 5));

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 5);
                    $order->products()->attach($product->id, [
                        'quantity' => $quantity,
                        'total' => $quantity * $product->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
        });

       


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
