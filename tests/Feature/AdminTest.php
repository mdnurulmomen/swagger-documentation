<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends TestCase
{
    public function test_login_method_returns_successfull_response(): void
    {
        $admin = User::factory()->create([
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);

        $payload = ['email'=> $admin->email, 'password'=> 'password', 'is_admin'=> 1];

        $response = $this->postJson(route('admin.login', $payload));

        $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data');
    }

    /**
     * A basic feature test example.
     */
    public function test_store_method_returns_successfull_response(): void
    {
        $admin = User::factory()->create([
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);

        $token = JWTAuth::attempt(['email'=> $admin->email, 'password'=> 'password', 'is_admin'=> 1]);

        $payload = [
            'uuid' => Str::uuid(),
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'is_admin' => rand(0, 1),
            'is_marketing' => rand(0, 1),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => fake()->address(),
            'phone_number' => fake()->unique()->e164PhoneNumber(),
            'remember_token' => Str::random(10),
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson(route('admin.users.store', $payload));

        $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('success', true);
    }

    /**
     * A basic feature test example.
     */
    public function test_user_list_method_returns_proper_response(): void
    {
        $admin = User::factory()->create([
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);

        $token = JWTAuth::attempt(['email'=> $admin->email, 'password'=> 'password', 'is_admin'=> 1]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson(route('admin.users.index'));

        $response->assertJsonCount(1, 'data');
    }
}
