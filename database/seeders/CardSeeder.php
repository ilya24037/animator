<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Card::create([
                'title' => "Массаж №{$i}",
                'image' => "/images/cards/{$i}.jpg",
                'price' => rand(1000, 3000),
                'city' => ['Москва', 'СПб', 'Казань', 'Екатеринбург'][rand(0, 3)],
            ]);
        }
    }
}
