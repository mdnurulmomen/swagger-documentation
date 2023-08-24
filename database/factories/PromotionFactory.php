<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'title' => fake()->title(),
            'content' => Str::random(10),
            'metadata' => json_encode([
                fake()->randomElement([
                    "valid_from" => "date(Y-m-d)",
                    "valid_to" => "date(Y-m-d)",
                    "image" => "UUID from petshop.files"
                ])
             ]),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
