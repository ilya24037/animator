<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdServiceSeeder extends Seeder
{
    public function run(): void
    {
        $ads = DB::table('ads')->pluck('id');
        $services = DB::table('services')->pluck('id');

        foreach ($ads as $ad_id) {
            // Назначаем случайную услугу 1–2 объявлениям
            if (rand(0, 1)) {
                $service_id = $services->random();
                $started_at = now();
                $expires_at = now()->addDays(rand(1, 7));

                DB::table('ad_services')->insert([
                    'ad_id' => $ad_id,
                    'service_id' => $service_id,
                    'started_at' => $started_at,
                    'expires_at' => $expires_at,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
