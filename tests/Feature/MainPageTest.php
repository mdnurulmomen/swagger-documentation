<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MainPageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_promotion_list_method_returns_proper_response(): void
    {
        $categories = Promotion::factory()->count(3)->create();

        $response = $this->getJson(route('main.promotions.index'));

        $response->assertJsonCount(3, 'data');
    }
}
