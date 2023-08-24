<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->title();

        return [
            'uuid' => Str::uuid(),
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
        ];
    }
}
