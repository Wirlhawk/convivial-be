<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CollaborateHere>
 */
class CollaborateHereFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => '',
            'title' => $this->faker->catchPhrase(),
            'desc' => $this->faker->paragraph(2),
        ];
    }
}
