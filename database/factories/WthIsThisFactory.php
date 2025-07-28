<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WthIsThis>
 */
class WthIsThisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'desc' => $this->faker->paragraph(2),
            'img' => '',
            'short_desc' => $this->faker->sentence(8),
            'subtitle' => $this->faker->sentence(4),
        ];
    }
}
