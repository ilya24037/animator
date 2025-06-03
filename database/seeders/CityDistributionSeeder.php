<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Animator;

class CityDistributionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ru_RU');

        // Распределение по городам
        $cities = [
            'Москва' => 60,
            'Санкт-Петербург' => 40,
            'Казань' => 30,
            'Екатеринбург' => 37,
            'Новосибирск' => 25,
            'Пермь' => 20,
        ];

        // Очистим таблицу
        DB::table('animators')->truncate();

        foreach ($cities as $city => $count) {
            for ($i = 0; $i < $count; $i++) {
                Animator::create([
                    'name' => $faker->name(),
                    'age' => $faker->numberBetween(20, 45),
                    'height' => $faker->numberBetween(160, 195),
                    'weight' => $faker->numberBetween(50, 100),
                    'price' => $faker->numberBetween(2000, 10000),
                    'rating' => round($faker->randomFloat(1, 3.5, 5.0), 1),
                    'reviews' => $faker->numberBetween(5, 300),
                    'city' => $city,
                    'image' => 'default.jpg',
                ]);
            }
        }
    }
}
