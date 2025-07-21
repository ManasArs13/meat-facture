<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_categories()
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                        "links" => ["self"]
                    ]
                ],
                'meta' => ['filters']
            ]);
    }

    #[Test]
    public function it_can_filter_categories()
    {
        $this->withoutExceptionHandling();

        $category1 = Category::factory()->create(['name' => 'Zebra']);
        $category2 = Category::factory()->create(['name' => 'Apple']);

        $response = $this->getJson('/api/categories?name=Zebra');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Zebra'])
            ->assertJsonMissing(['name' => 'Apple']);
    }

    #[Test]
    public function it_can_sort_categories()
    {
        $this->withoutExceptionHandling();

        $category1 = Category::factory()->create(['name' => 'Zebra']);
        $category2 = Category::factory()->create(['name' => 'Apple']);

        $response = $this->getJson('/api/categories?sort_by=name&sort_dir=asc');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Apple')
            ->assertJsonPath('data.1.name', 'Zebra');
    }

    #[Test]
    public function it_can_show_single_category()
    {
        $this->withoutExceptionHandling();

        $category = Category::find(1);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                    "links" => ["self"]
                ]
            ])
            ->assertJsonFragment(['name' => $category->name]);
    }
}
