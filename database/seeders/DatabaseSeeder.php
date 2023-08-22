<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'uuid' => Str::uuid(),
            'first_name' => 'User',
            'last_name' => 'One',
            'is_admin' => 1,
            'email' => 'admin@buckhill.co.uk',
            'password' => Hash::make('password'),
            'address' => 'User Address',
            'phone_number' => 'User Phone Number',
        ]);
    }
}
