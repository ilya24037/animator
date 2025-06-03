<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;
use Illuminate\Support\Str;

class AdSeeder extends Seeder
{
    public function run(): void
    {
        $cities = ['Москва', 'Санкт-Петербург', 'Казань', 'Екатеринбург', 'Новосибирск'];
        $categories = ['Массаж', 'Спа-услуги', 'Маникюр'];
        $subcategories = ['Антицеллюлитный', 'Расслабляющий', 'Классический'];

        for ($i = 1; $i <= 30; $i++) {
            Ad::create([
                'user_id' => 1,
                'title' => 'Услуга №' . $i,
                'description' => 'Профессиональный массаж и спа-процедуры. Индивидуальный подход.',
                'price' => rand(1500, 8000),
                'city' => $cities[array_rand($cities)],
                'category' => $categories[array_rand($categories)],
                'subcategory' => $subcategories[array_rand($subcategories)],
                'age' => rand(22, 45),
                'height' => rand(155, 190),
                'weight' => rand(50, 100),
                'rating' => round(rand(40, 50) / 10, 1),
                'reviews' => rand(20, 300),
                'gender' => ['male', 'female'][rand(0, 1)],
                'image' => '/images/ads/' . rand(1, 5) . '.jpg',
                'is_paid' => rand(0, 1),
                'published_at' => now(),
            ]);
        }
    }
}
