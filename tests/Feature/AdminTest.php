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
}
