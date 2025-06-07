<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавляем в таблицу animators недостающие поля,
     * которые уже перечислены в модели (fillable / casts).
     */
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // строковые / текстовые
            $table->string('photo_folder')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('specialization')->nullable();
            $table->string('image')->nullable();

            // перечисления / статусы
            $table->string('type')->default('private');
            $table->string('status')->default('draft');

            // JSON-поля
            $table->json('work_format')->nullable();
            $table->json('price_list')->nullable();
            $table->json('actions_data')->nullable();
            $table->json('geo_data')->nullable();
            $table->json('contacts_data')->nullable();
            $table->json('zones')->nullable();
            $table->json('services')->nullable();
            $table->json('heroes')->nullable();

            // числовые
            $table->float('rating', 3, 2)->default(0);   // 0-9.99
            $table->unsignedInteger('reviews')->default(0);

            // булевы
            $table->boolean('is_online')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('quick_booking')->default(false);
            $table->boolean('terms_accepted')->default(false);
        });
    }

    /**
     * Обратный откат: удаляем добавленные поля.
     */
    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $table->dropColumn([
                'photo_folder', 'city', 'address', 'email', 'specialization',
                'image', 'type', 'status', 'work_format', 'price_list',
                'actions_data', 'geo_data', 'contacts_data', 'zones',
                'services', 'heroes', 'rating', 'reviews', 'is_online',
                'is_verified', 'quick_booking', 'terms_accepted',
            ]);
        });
    }
};
