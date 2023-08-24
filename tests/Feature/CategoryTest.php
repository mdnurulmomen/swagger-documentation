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
        Category::factory()->count(3)->create();

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

        $categoryPayload = [
            'uuid' => Str::uuid(),
            'title' => $title,
            'slug' => str_replace(' ', '-', $title),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson(route('categories.store', $categoryPayload));

        $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('success', true);
    }

    /**
     * A basic feature test example.
     */
    public function test_update_method_returns_successfull_response(): void
    {
        $admin = User::factory()->create([
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);

        $token = JWTAuth::attempt(['email'=> $admin->email, 'password'=> 'password', 'is_admin'=> 1]);

        $newCategory = Category::create([
            'title' => 'Category Title',
            'slug' => 'Category-Title'
        ]);

        $categoryUpdatingPayload = [
            'title' => 'Category Title Updated'
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->putJson('api/v1/category/'.$newCategory->uuid, $categoryUpdatingPayload);

        $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJson(['success' => true]);
    }

    /**
     * A basic feature test example.
     */
    public function test_delete_method_returns_successfull_response(): void
    {
        $admin = User::factory()->create([
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);

        $token = JWTAuth::attempt(['email'=> $admin->email, 'password'=> 'password', 'is_admin'=> 1]);

        $newCategory = Category::create([
            'title' => 'Category Title',
            'slug' => 'Category-Title'
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson(route('categories.destroy', ['uuid' => $newCategory->uuid]));

        $response->assertOk()
        ->assertJson(['success' => true]);
    }
}
