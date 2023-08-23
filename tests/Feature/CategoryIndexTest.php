<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryIndexTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index_method_returns_proper_response(): void
    {
        $categories = Category::factory()->count(3)->create();

        $response = $this->getJson(route('categories.index'));

        $response->assertJsonCount(3, 'data');
    }

    /**
     * A basic feature test example.
     */
    public function test_show_method_returns_proper_response(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson(route('categories.show', ['uuid' => $category->uuid]));

        $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.id', $category->id);
    }
}
