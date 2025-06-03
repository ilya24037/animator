<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AnimatorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'price' => fake()->numberBetween(1000, 20000),
            'age' => fake()->numberBetween(18, 40),
            'height' => fake()->numberBetween(150, 190),
        ];
    }
}
