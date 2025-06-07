<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Если колонки user_id ещё нет — добавляем её как nullable
            if (! Schema::hasColumn('animators', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained('users')
                      ->cascadeOnDelete();
            }

            // Если колонки status ещё нет — добавляем её со значением по умолчанию 'draft'
            if (! Schema::hasColumn('animators', 'status')) {
                $table->string('status')->default('draft');
            }

            // Добавляем индекс на [user_id, status], если он ещё не создан
            // (Laravel автоматически пропустит это, если индекс уже есть)
            $table->index(['user_id', 'status'], 'animators_user_id_status_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Удаляем индекс, если он существует
            if (Schema::hasColumn('animators', 'user_id') && Schema::hasColumn('animators', 'status')) {
                $table->dropIndex('animators_user_id_status_index');
            }

            // Если внешний ключ user_id существует — удаляем его
            if (Schema::hasColumn('animators', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            // Если колонка status существует — удаляем её
            if (Schema::hasColumn('animators', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
