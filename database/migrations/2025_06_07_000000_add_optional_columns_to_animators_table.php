<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавляем ТОЛЬКО отсутствующие поля в таблицу animators.
     */
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {

            /* ---- строковые / текстовые ---- */
            if (!Schema::hasColumn('animators', 'photo_folder')) {
                $table->string('photo_folder')->nullable();
            }
            if (!Schema::hasColumn('animators', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('animators', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('animators', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('animators', 'specialization')) {
                $table->string('specialization')->nullable();
            }
            if (!Schema::hasColumn('animators', 'image')) {
                $table->string('image')->nullable();
            }

            /* ---- перечисления / статусы ---- */
            if (!Schema::hasColumn('animators', 'type')) {
                $table->string('type')->default('private');
            }
            if (!Schema::hasColumn('animators', 'status')) {
                $table->string('status')->default('draft');
            }

            /* ---- JSON-поля ---- */
            foreach ([
                'work_format', 'price_list', 'actions_data', 'geo_data',
                'contacts_data', 'zones', 'services', 'heroes',
            ] as $jsonColumn) {
                if (!Schema::hasColumn('animators', $jsonColumn)) {
                    $table->json($jsonColumn)->nullable();
                }
            }

            /* ---- числовые ---- */
            if (!Schema::hasColumn('animators', 'rating')) {
                $table->float('rating', 3, 2)->default(0);   // 0–9.99
            }
            if (!Schema::hasColumn('animators', 'reviews')) {
                $table->unsignedInteger('reviews')->default(0);
            }

            /* ---- булевы ---- */
            foreach ([
                'is_online', 'is_verified', 'quick_booking', 'terms_accepted',
            ] as $boolColumn) {
                if (!Schema::hasColumn('animators', $boolColumn)) {
                    $table->boolean($boolColumn)->default(false);
                }
            }
        });
    }

    /**
     * Откат: удаляем те же поля, но только если они существуют.
     */
    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $columns = [
                'photo_folder', 'city', 'address', 'email', 'specialization',
                'image', 'type', 'status', 'work_format', 'price_list',
                'actions_data', 'geo_data', 'contacts_data', 'zones',
                'services', 'heroes', 'rating', 'reviews', 'is_online',
                'is_verified', 'quick_booking', 'terms_accepted',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('animators', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
