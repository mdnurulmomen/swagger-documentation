<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();           // to avoid duplicate values

        // Super-Admin
        User::create([
            'uuid' => Str::uuid(),
            'first_name' => 'User',
            'last_name' => 'One',
            'is_admin' => 1,
            'email' => 'admin@buckhill.co.uk',
            'password' => Hash::make('admin'),
            'address' => 'User Address',
            'phone_number' => 'User Phone Number',
        ]);

        User::factory(10)->create();
        Category::factory(10)->create();
        Post::factory(10)->create();
        Promotion::factory(10)->create();
    }
}
