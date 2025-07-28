<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WhatUpDetail>
 */
class WhatUpDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $images = [];
        for ($i = 0; $i < rand(2, 4); $i++) {
            $images[] = 'whatup_images/' . $this->faker->image('storage/app/public/whatup_images', 640, 480, 'nature', false);
        }
        return [
            'title' => $this->faker->sentence(3),
            'short_desc' => $this->faker->sentence(8),
            'images' => json_encode($images),
            'desc' => $this->faker->paragraph(3),
        ];
    }
}
