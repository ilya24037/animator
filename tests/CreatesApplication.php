<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

/**
 * Подготавливает экземпляр приложения для юнит-/feature-тестов.
 * Это стандартная реализация из свежего Laravel 10 без изменений.
 */
trait CreatesApplication
{
    /**
     * Создать и инициализировать приложение.
     */
    public function createApplication()
    {
        // Подтягиваем bootstrap-файл фреймворка
        $app = require __DIR__ . '/../bootstrap/app.php';

        // Полная инициализация: конфиг, провайдеры и т. д.
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
