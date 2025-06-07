<?php
// test-application.php
// Запустите: php test-application.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Animator;

echo "🧪 ФИНАЛЬНОЕ ТЕСТИРОВАНИЕ ПРИЛОЖЕНИЯ\n";
echo "=====================================\n\n";

// 1. Проверка таблиц
echo "📋 1. ПРОВЕРКА ТАБЛИЦ:\n";
$tables = ['users', 'animators', 'items', 'cards', 'media', 'categories', 'subcategories'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        try {
            $count = DB::table($table)->count();
            echo "✅ $table - существует (записей: $count)\n";
        } catch (Exception $e) {
            echo "⚠️  $table - ошибка при подсчете записей\n";
        }
    } else {
        echo "❌ $table - не найдена\n";
    }
}

// 2. Создание/проверка тестового пользователя
echo "\n📋 2. ТЕСТОВЫЙ ПОЛЬЗОВАТЕЛЬ:\n";
$testUser = User::firstOrCreate(
    ['email' => 'test@example.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('password'),
        'email_verified_at' => now()
    ]
);
echo "✅ Email: {$testUser->email}\n";
echo "✅ ID: {$testUser->id}\n";
echo "✅ Пароль: password\n";

// 3. Создание тестового аниматора
echo "\n📋 3. ТЕСТОВЫЙ АНИМАТОР:\n";
try {
    $animator = Animator::firstOrCreate(
        ['user_id' => $testUser->id, 'title' => 'Тестовое объявление'],
        [
            'name' => 'Тестовый массажист',
            'description' => 'Профессиональный массаж с выездом на дом',
            'price' => 3000,
            'city' => 'Москва',
            'city_id' => 1,
            'status' => 'active',
            'type' => 'private',
            'is_online' => true,
            'is_verified' => false,
            'zones' => 'city',
            'services' => json_encode(['Классический массаж', 'Расслабляющий массаж']),
            'quick_booking' => true,
            'terms_accepted' => true
        ]
    );
    echo "✅ Создан аниматор ID: {$animator->id}\n";
    echo "✅ Название: {$animator->title}\n";
    echo "✅ Статус: {$animator->status}\n";
} catch (Exception $e) {
    echo "❌ Ошибка создания аниматора: " . $e->getMessage() . "\n";
}

// 4. Проверка маршрутов
echo "\n📋 4. ОСНОВНЫЕ МАРШРУТЫ:\n";
$routes = [
    '/' => 'GET',
    '/login' => 'GET',
    '/register' => 'GET',
    '/dashboard' => 'GET',
    '/animators/create' => 'GET',
    '/profile/items/draft/all' => 'GET'
];

foreach ($routes as $uri => $method) {
    $route = app('router')->getRoutes()->match(
        app('request')->create($uri, $method)
    );
    if ($route) {
        echo "✅ $method $uri -> " . $route->getActionName() . "\n";
    } else {
        echo "❌ $method $uri - маршрут не найден\n";
    }
}

// 5. Проверка конфигурации
echo "\n📋 5. КОНФИГУРАЦИЯ:\n";
echo "✅ APP_ENV: " . config('app.env') . "\n";
echo "✅ APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "✅ DB_CONNECTION: " . config('database.default') . "\n";
echo "✅ SESSION_DRIVER: " . config('session.driver') . "\n";

// 6. Рекомендации
echo "\n📋 6. РЕКОМЕНДАЦИИ ДЛЯ ЗАПУСКА:\n";
echo "1. Убедитесь, что в файле app/Models/Media.php есть строка:\n";
echo "   protected \$table = 'media';\n\n";
echo "2. Запустите сервер:\n";
echo "   php artisan serve\n\n";
echo "3. В новом терминале запустите Vite:\n";
echo "   npm install\n";
echo "   npm run dev\n\n";
echo "4. Откройте в браузере:\n";
echo "   http://localhost:8000\n\n";
echo "5. Войдите с учетными данными:\n";
echo "   Email: test@example.com\n";
echo "   Пароль: password\n\n";

echo "✨ Тестирование завершено!\n";