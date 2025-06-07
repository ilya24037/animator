<?php
// skip-migrations.php
// Создайте этот файл в корне проекта (там где файл artisan)
// Запустите: php skip-migrations.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📝 Отмечаем миграции как выполненные...\n\n";

// Получаем последний batch
$batch = DB::table('migrations')->max('batch') ?? 0;
$newBatch = $batch + 1;

// Список миграций для пропуска
$migrations = [
    '2025_05_16_075410_add_weight_to_animators_table',
    '2025_05_16_080112_add_reviews_to_animators_table',
    '2025_05_16_080553_add_city_to_animators_table',
    '2025_05_16_080845_add_image_to_animators_table',
    '2025_05_16_101642_add_type_to_animators_table',
    '2025_06_01_000000_add_user_id_and_status_to_animators_table',
];

foreach ($migrations as $migration) {
    try {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $newBatch
        ]);
        echo "✅ Отмечена: $migration\n";
    } catch (Exception $e) {
        echo "⏭️  Уже отмечена: $migration\n";
    }
}

echo "\n✨ Готово! Теперь запустите 'php artisan migrate'\n";