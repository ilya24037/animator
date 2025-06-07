<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animator;
use App\Models\User;
use Faker\Factory as Faker;

class AnimatorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $cities = ['Москва', 'Казань', 'Екатеринбург', 'Санкт-Петербург', 'Новосибирск', 'Пермь'];
        
        // Получаем первого пользователя или создаем
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password')
            ]);
        }

        foreach ($cities as $city) {
            // Создаем несколько аниматоров для каждого города
            for ($i = 0; $i < 3; $i++) {
                Animator::create([
                    'user_id' => $user->id,
                    'name' => $faker->name(),
                    'title' => 'Массаж ' . $faker->randomElement(['классический', 'спортивный', 'расслабляющий', 'антицеллюлитный']),
                    'description' => $faker->paragraph(3),
                    'age' => rand(20, 40),
                    'height' => rand(160, 190),
                    'weight' => rand(50, 90),
                    'price' => rand(2000, 8000),
                    'rating' => round(rand(40, 50) / 10, 1),
                    'reviews' => rand(10, 200),
                    'city' => $city,
                    'type' => $faker->randomElement(['private', 'company']),
                    'image' => 'default.jpg',
                    'status' => 'published', // Важно! Устанавливаем статус published
                    'is_online' => $faker->boolean(70), // 70% вероятность быть онлайн
                    'is_verified' => $faker->boolean(40), // 40% вероятность быть верифицированным
                    'specialization' => $faker->randomElement(['Массаж', 'Спа-процедуры', 'Фитнес']),
                ]);
            }
        }
        
        echo "Создано " . ($cities->count() * 3) . " аниматоров\n";
    }
}