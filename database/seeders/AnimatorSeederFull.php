<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animator;
use Faker\Factory as Faker;

class AnimatorSeederFull extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ru_RU');

        Animator::truncate();

        $cities = ['Москва', 'Санкт-Петербург', 'Казань', 'Екатеринбург', 'Новосибирск', 'Пермь'];
        $types = ['private', 'company'];

        foreach (range(1, 150) as $i) {
            Animator::create([
                'name' => $faker->name(),
                'age' => rand(20, 45),
                'height' => rand(160, 195),
                'weight' => rand(50, 100),
                'price' => rand(1500, 12000),
                'rating' => round($faker->randomFloat(1, 3.5, 5.0), 1),
                'reviews' => rand(5, 300),
                'city' => $faker->randomElement($cities),
                'type' => $faker->randomElement($types),
                'is_online' => $faker->boolean(),
                'is_verified' => $faker->boolean(),
                'image' => 'default.jpg',
            ]);
        }
    }
}
