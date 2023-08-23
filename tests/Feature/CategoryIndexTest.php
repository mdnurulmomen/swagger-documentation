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
}
