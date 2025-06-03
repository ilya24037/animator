<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animator;
use Faker\Factory as Faker;

class AnimatorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $cities = ['Москва', 'Казань', 'Екатеринбург', 'Санкт-Петербург', 'Новосибирск', 'Пермь'];

        foreach ($cities as $city) {
            Animator::create([
                'name' => $faker->name(),
                'age' => rand(20, 40),
                'height' => rand(160, 190),
                'weight' => rand(50, 90),
                'price' => rand(2000, 8000),
                'rating' => round(rand(40, 50) / 10, 1),
                'reviews' => rand(10, 200),
                'city' => $city,
                'type' => $faker->randomElement(['private', 'company']),
                'image' => 'default.jpg',
            ]);
        }
    }
}
