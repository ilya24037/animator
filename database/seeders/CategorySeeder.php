<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Категории и подкатегории
        $data = [
            'Массаж' => ['Антицеллюлитный', 'Спортивный', 'Расслабляющий', 'Классический'],
            'Спа-услуги' => ['Стоун-терапия', 'Обертывания', 'Тайский массаж'],
            'Уход за лицом' => ['Чистка', 'Пилинг', 'Увлажнение']
        ];

        foreach ($data as $category => $subcategories) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($subcategories as $sub) {
                DB::table('subcategories')->insert([
                    'category_id' => $categoryId,
                    'name' => $sub,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
