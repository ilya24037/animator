<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Категории
            if (!Schema::hasColumn('animators', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('animators', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');
            }
            
            // Цены
            if (!Schema::hasColumn('animators', 'price_type')) {
                $table->string('price_type', 20)->default('fixed')->after('price');
            }
            if (!Schema::hasColumn('animators', 'price_unit')) {
                $table->string('price_unit', 20)->default('service')->after('price_type');
            }
            
            // Локация
            if (!Schema::hasColumn('animators', 'districts')) {
                $table->json('districts')->nullable()->after('zones');
            }
            if (!Schema::hasColumn('animators', 'service_radius')) {
                $table->integer('service_radius')->nullable()->after('districts');
            }
            
            // Контакты
            if (!Schema::hasColumn('animators', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('animators', 'contact_methods')) {
                $table->json('contact_methods')->nullable()->after('contact_name');
            }
            if (!Schema::hasColumn('animators', 'show_in_messages')) {
                $table->boolean('show_in_messages')->default(true)->after('contact_methods');
            }
            
            // Дополнительные услуги
            if (!Schema::hasColumn('animators', 'online_service')) {
                $table->boolean('online_service')->default(false)->after('quick_booking');
            }
            if (!Schema::hasColumn('animators', 'home_visit')) {
                $table->boolean('home_visit')->default(true)->after('online_service');
            }
            
            // Расписание
            if (!Schema::hasColumn('animators', 'schedule')) {
                $table->json('schedule')->nullable()->after('home_visit');
            }
            
            // Медиа
            if (!Schema::hasColumn('animators', 'youtube_url')) {
                $table->string('youtube_url')->nullable()->after('image');
            }
            
            // Статистика
            if (!Schema::hasColumn('animators', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('reviews');
            }
            if (!Schema::hasColumn('animators', 'favorites_count')) {
                $table->unsignedInteger('favorites_count')->default(0)->after('views');
            }
            if (!Schema::hasColumn('animators', 'messages_count')) {
                $table->unsignedInteger('messages_count')->default(0)->after('favorites_count');
            }
            
            // Индексы для производительности
            if (!Schema::hasIndex('animators', 'animators_category_status_index')) {
                $table->index(['category_id', 'status'], 'animators_category_status_index');
            }
            if (!Schema::hasIndex('animators', 'animators_city_status_index')) {
                $table->index(['city', 'status'], 'animators_city_status_index');
            }
            if (!Schema::hasIndex('animators', 'animators_price_index')) {
                $table->index('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Удаляем индексы
            $table->dropIndex(['category_id', 'status']);
            $table->dropIndex(['city', 'status']);
            $table->dropIndex(['price']);
            
            // Удаляем колонки
            $columns = [
                'category_id', 'subcategory_id', 'price_type', 'price_unit',
                'districts', 'service_radius', 'contact_name', 'contact_methods',
                'show_in_messages', 'online_service', 'home_visit', 'schedule',
                'youtube_url', 'views', 'favorites_count', 'messages_count'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('animators', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};