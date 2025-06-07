<?php
// check-project-status.php
// Запускать из корня проекта: php check-project-status.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔍 ПРОВЕРКА СОСТОЯНИЯ ПРОЕКТА\n";
echo "==============================\n\n";

// 1. Проверка миграций
echo "📋 1. СОСТОЯНИЕ МИГРАЦИЙ:\n";
$migrations = DB::table('migrations')->orderBy('batch')->get();
$lastBatch = $migrations->max('batch');
echo "Всего миграций: " . $migrations->count() . "\n";
echo "Последний batch: " . $lastBatch . "\n\n";

// Проверяем дубликаты
$duplicates = [];
foreach ($migrations as $m1) {
    foreach ($migrations as $m2) {
        if ($m1->id < $m2->id && 
            strpos($m1->migration, 'add_user_id_and_status') !== false && 
            strpos($m2->migration, 'add_user_id_and_status') !== false) {
            $duplicates[] = [$m1->migration, $m2->migration];
        }
    }
}

if (count($duplicates) > 0) {
    echo "⚠️  Найдены дублирующиеся миграции:\n";
    foreach ($duplicates as $dup) {
        echo "   - " . $dup[0] . "\n";
        echo "   - " . $dup[1] . "\n";
    }
} else {
    echo "✅ Дублирующихся миграций не найдено\n";
}

// 2. Проверка структуры таблицы animators
echo "\n📋 2. СТРУКТУРА ТАБЛИЦЫ ANIMATORS:\n";
if (Schema::hasTable('animators')) {
    $columns = Schema::getColumnListing('animators');
    echo "Всего колонок: " . count($columns) . "\n";
    
    // Проверяем важные поля
    $requiredFields = [
        'user_id', 'status', 'city_id', 'about', 'zones', 
        'services', 'heroes', 'quick_booking', 'terms_accepted'
    ];
    
    echo "\nПроверка обязательных полей:\n";
    foreach ($requiredFields as $field) {
        if (in_array($field, $columns)) {
            echo "✅ $field - есть\n";
        } else {
            echo "❌ $field - отсутствует\n";
        }
    }
} else {
    echo "❌ Таблица animators не найдена!\n";
}

// 3. Проверка файлов миграций
echo "\n📋 3. ФАЙЛЫ МИГРАЦИЙ:\n";
$migrationFiles = glob('database/migrations/*.php');
$problemFiles = [];

foreach ($migrationFiles as $file) {
    $filename = basename($file);
    // Проверяем на дубликаты
    if (strpos($filename, '184628_add_user_id_and_status') !== false ||
        strpos($filename, '000001_add_is_online_and_verified') !== false ||
        strpos($filename, '112246_add_is_online_and_verified') !== false) {
        $problemFiles[] = $filename;
    }
}

if (count($problemFiles) > 0) {
    echo "⚠️  Найдены файлы для удаления:\n";
    foreach ($problemFiles as $file) {
        echo "   ❌ $file\n";
    }
} else {
    echo "✅ Проблемных файлов миграций не найдено\n";
}

// 4. Проверка моделей
echo "\n📋 4. МОДЕЛИ:\n";
$models = ['Animator', 'Item', 'Ad', 'Card', 'Media'];
foreach ($models as $model) {
    $path = "app/Models/{$model}.php";
    if (file_exists($path)) {
        $count = DB::table(strtolower($model) . 's')->count();
        echo "✅ $model - найдена (записей: $count)\n";
    } else {
        echo "❌ $model - не найдена\n";
    }
}

// 5. Проверка JavaScript файлов
echo "\n📋 5. JAVASCRIPT ФАЙЛЫ:\n";
$jsFiles = [
    'resources/js/Stores/useLocationStore.js' => 'localStorage',
    'resources/js/Pages/Animators/Create.vue' => 'axios',
    'resources/js/bootstrap.js' => 'axios'
];

foreach ($jsFiles as $file => $searchFor) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, $searchFor) !== false) {
            echo "⚠️  $file - содержит '$searchFor'\n";
        } else {
            echo "✅ $file - не содержит '$searchFor'\n";
        }
    } else {
        echo "❌ $file - не найден\n";
    }
}

// 6. Итоговые рекомендации
echo "\n📋 РЕКОМЕНДАЦИИ:\n";
echo "1. Удалите дублирующиеся файлы миграций из папки database/migrations\n";
echo "2. Замените localStorage на cookies в useLocationStore.js\n";
echo "3. Рассмотрите унификацию моделей Animator/Item/Ad в одну модель Listing\n";
echo "4. Замените axios на Inertia router для консистентности\n";
echo "5. Запустите 'php artisan migrate:status' для финальной проверки\n";

echo "\n✨ Проверка завершена!\n";