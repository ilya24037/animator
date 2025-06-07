<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Добавляем title если его нет
            if (!Schema::hasColumn('animators', 'title')) {
                $table->string('title')->nullable()->after('name');
            }
            
            // Добавляем description если его нет
            if (!Schema::hasColumn('animators', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            
            // Добавляем city_id если его нет
            if (!Schema::hasColumn('animators', 'city_id')) {
                $table->unsignedBigInteger('city_id')->default(1)->after('user_id');
            }
            
            // Добавляем about - теперь проверяем наличие description
            if (!Schema::hasColumn('animators', 'about')) {
                if (Schema::hasColumn('animators', 'description')) {
                    $table->text('about')->nullable()->after('description');
                } else {
                    $table->text('about')->nullable();
                }
            }
            
            // Добавляем zones
            if (!Schema::hasColumn('animators', 'zones')) {
                if (Schema::hasColumn('animators', 'city')) {
                    $table->string('zones')->default('city')->after('city');
                } else {
                    $table->string('zones')->default('city');
                }
            }
            
            // JSON поля для хранения данных формы
            if (!Schema::hasColumn('animators', 'services')) {
                $table->json('services')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'heroes')) {
                $table->text('heroes')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'quick_booking')) {
                $table->boolean('quick_booking')->default(false);
            }
            
            if (!Schema::hasColumn('animators', 'terms_accepted')) {
                $table->boolean('terms_accepted')->default(false);
            }
            
            // Поля для хранения JSON данных каждого шага формы
            if (!Schema::hasColumn('animators', 'work_format')) {
                $table->json('work_format')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'price_list')) {
                $table->json('price_list')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'actions_data')) {
                $table->json('actions_data')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'geo_data')) {
                $table->json('geo_data')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'contacts_data')) {
                $table->json('contacts_data')->nullable();
            }
            
            // Дополнительные поля для удобства
            if (!Schema::hasColumn('animators', 'address')) {
                $table->text('address')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'phone')) {
                $table->string('phone')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'email')) {
                $table->string('email')->nullable();
            }
            
            if (!Schema::hasColumn('animators', 'specialization')) {
                $table->string('specialization')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $columns = [
                'title', 'description', 'city_id', 'about', 'zones', 'services', 
                'heroes', 'quick_booking', 'terms_accepted', 'work_format', 
                'price_list', 'actions_data', 'geo_data', 'contacts_data',
                'address', 'phone', 'email', 'specialization'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('animators', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};