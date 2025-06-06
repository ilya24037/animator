<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Добавляем недостающие поля, если их еще нет
            if (!Schema::hasColumn('animators', 'city_id')) {
                $table->unsignedBigInteger('city_id')->default(1)->after('user_id');
            }
            
            if (!Schema::hasColumn('animators', 'about')) {
                $table->text('about')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('animators', 'zones')) {
                $table->string('zones')->default('city')->after('city');
            }
            
            if (!Schema::hasColumn('animators', 'services')) {
                $table->json('services')->nullable()->after('specialization');
            }
            
            if (!Schema::hasColumn('animators', 'heroes')) {
                $table->text('heroes')->nullable()->after('services');
            }
            
            if (!Schema::hasColumn('animators', 'quick_booking')) {
                $table->boolean('quick_booking')->default(false)->after('is_verified');
            }
            
            if (!Schema::hasColumn('animators', 'terms_accepted')) {
                $table->boolean('terms_accepted')->default(false)->after('quick_booking');
            }
        });
    }

    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $columns = ['city_id', 'about', 'zones', 'services', 'heroes', 'quick_booking', 'terms_accepted'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('animators', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};