<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_products()
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        "description",
                        "category",
                        "price",
                        "is_available",
                        'created_at',
                        'updated_at',
                        "links" => ["self"]
                    ]
                ],
                'meta' => ['filters']
            ]);
    }

    #[Test]
    public function it_can_filter_products()
    {
        $product_1 = Product::factory()->create(['name' => 'Zebra', 'category_id' => 1]);
        $product_2 = Product::factory()->create(['name' => 'Apple', 'category_id' => 1]);

        $response = $this->getJson('/api/products?name=Zebra');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Zebra'])
            ->assertJsonMissing(['name' => 'Apple']);
    }

    #[Test]
    public function it_can_sort_products()
    {
        $product_1 = Product::factory()->create(['name' => 'Zebra', 'category_id' => 1]);
        $product_2 = Product::factory()->create(['name' => 'Apple', 'category_id' => 1]);

        $response = $this->getJson('/api/products?sort_by=name&sort_dir=asc');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Apple')
            ->assertJsonPath('data.1.name', 'Zebra');
    }

    #[Test]
    public function it_can_show_single_products()
    {
        $products = Product::find(1);

        $response = $this->getJson("/api/products/{$products->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    "description",
                    "category",
                    "price",
                    "is_available",
                    'created_at',
                    'updated_at',
                    "links" => ["self"]
                ]
            ])
            ->assertJsonFragment(['name' => $products->name]);
    }
}
