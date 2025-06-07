<?php
// skip-existing-migrations.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Отмечаем уже примененные миграции как выполненные...\n\n";

// Список миграций, которые фактически уже применены
$already_applied = [
    '2025_05_16_075410_add_weight_to_animators_table',
    '2025_05_16_080112_add_reviews_to_animators_table',
    '2025_05_16_080553_add_city_to_animators_table',
    '2025_05_16_080845_add_image_to_animators_table',
    '2025_05_16_101642_add_type_to_animators_table',
    '2025_06_01_000000_add_user_id_and_status_to_animators_table',
];

// Получаем последний batch
$last_batch = DB::table('migrations')->max('batch') ?? 0;
$new_batch = $last_batch + 1;

foreach ($already_applied as $migration) {
    // Проверяем, есть ли уже запись
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    
    if (!$exists) {
        // Добавляем запись о том, что миграция выполнена
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $new_batch
        ]);
        echo "✅ Отмечена как выполненная: $migration\n";
    } else {
        echo "⏭️  Уже в списке выполненных: $migration\n";
    }
}

echo "\n✨ Готово! Теперь запустите 'php artisan migrate' для оставшихся миграций.\n";