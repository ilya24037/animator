<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Поднятие в списке',
                'price' => 150,
                'duration_days' => 1,
            ],
            [
                'name' => 'Выделение цветом',
                'price' => 300,
                'duration_days' => 7,
            ],
            [
                'name' => 'Закрепление в топ',
                'price' => 500,
                'duration_days' => 3,
            ],
            [
                'name' => 'Премиум размещение',
                'price' => 999,
                'duration_days' => 7,
            ]
        ];

        foreach ($services as $service) {
            DB::table('services')->insert(array_merge($service, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
