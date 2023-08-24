<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class CategoryTest extends TestCase
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

    /**
     * A basic feature test example.
     */
    public function test_create_method_returns_successfull_response(): void
    {
        $admin = User::factory()->create([
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);

        $token = JWTAuth::attempt(['email'=> $admin->email, 'password'=> 'password', 'is_admin'=> 1]);

        $title = fake()->title();

        $categoryInputArray = [
            'uuid' => Str::uuid(),
            'title' => $title,
            'slug' => str_replace(' ', '-', $title),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson(route('categories.store', $categoryInputArray));

        $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('success', true);
    }
}
