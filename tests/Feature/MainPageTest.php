<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\Promotion;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

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

    /**
     * A basic feature test example.
     */
    public function test_post_list_method_returns_proper_response(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson(route('main.blogs.index'));

        $response->assertJsonCount(3, 'data');
    }

    /**
     * A basic feature test example.
     */
    public function test_post_show_method_returns_proper_response(): void
    {
        Post::truncate();

        $title = fake()->title();

        $newPost = Post::create([
            'title' => $title,
            'slug' => str_replace(' ', '-', $title),
            'content' => Str::random(10),
            'metadata' => json_encode([
                fake()->randomElement([
                    "author"=> "string",
                    "image"=> "UUID from petshop.files"
                ])
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->getJson(route('main.blogs.show', ['uuid' => $newPost->uuid]));

        $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.id', $newPost->id);
    }
}
