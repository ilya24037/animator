<?php
// fix-migrations.php
// Запускать из корня проекта: php fix-migrations.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Исправление проблем с миграциями...\n\n";

// Отключаем проверку внешних ключей
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
echo "✅ Проверка внешних ключей отключена\n";

// Удаляем таблицы в правильном порядке
$tablesToDrop = [
    'ad_services',
    'subcategories',
    'categories',
    'media',
    'ads',
    'services',
    'payments',
];

foreach ($tablesToDrop as $table) {
    if (Schema::hasTable($table)) {
        Schema::drop($table);
        echo "✅ Таблица $table удалена\n";
    } else {
        echo "⏭️  Таблица $table не существует\n";
    }
}

// Очищаем записи о миграциях из таблицы migrations
$migrationsToRemove = [
    '2025_06_06_055109_add_missing_fields_to_animators_table',
    '2025_06_04_000001_create_categories_table',
    '2025_06_04_000000_add_status_to_items_table',
    '2025_06_02_000000_create_media_table',
    '2025_06_01_184628_add_user_id_and_status_to_animators_table',
    '2025_06_01_000000_add_user_id_and_status_to_animators_table',
    '2025_05_17_000001_add_is_online_and_verified_to_animators_table',
    '2025_05_17_000000_add_filters_to_animators_table',
    '2025_05_16_112246_add_is_online_and_verified_to_animators_table',
    '2025_05_16_101642_add_type_to_animators_table',
    '2025_05_16_080845_add_image_to_animators_table',
    '2025_05_16_080553_add_city_to_animators_table',
    '2025_05_16_080112_add_reviews_to_animators_table',
    '2025_05_16_075410_add_weight_to_animators_table',
    '2025_05_16_071446_create_ad_services_table',
    '2025_05_16_070556_create_subcategories_table',
    '2025_05_16_000000_create_avito_tables',
];

foreach ($migrationsToRemove as $migration) {
    DB::table('migrations')->where('migration', $migration)->delete();
    echo "✅ Удалена запись о миграции: $migration\n";
}

// Включаем обратно проверку внешних ключей
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "\n✅ Проверка внешних ключей включена\n";

echo "\n✨ Готово! Теперь можно удалять дублирующиеся файлы миграций.\n";