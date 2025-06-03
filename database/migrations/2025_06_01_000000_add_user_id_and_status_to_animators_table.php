<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Если колонки user_id ещё нет — добавляем её как nullable (чтобы не упало на уже существующих записях).
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

            // **Убираем создание индекса** animators_user_id_status_index,
            // чтобы не было ошибки "Duplicate key name" при повторном запуске миграции.
            //
            // Если вам необходим этот индекс, вы можете создать его вручную
            // через отдельную миграцию или через консоль MySQL:
            //   ALTER TABLE animators ADD INDEX animators_user_id_status_index(user_id, status);
            //
            // Но здесь он удалён, чтобы миграция ни при каких обстоятельствах не упала с дублированием.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Если внешний ключ user_id существует — удаляем его вместе с колонкой
            if (Schema::hasColumn('animators', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            // Если колонка status существует — удаляем её
            if (Schema::hasColumn('animators', 'status')) {
                $table->dropColumn('status');
            }

            // Заметьте: здесь мы не удаляем индекс, поскольку в up() его не создавали.
        });
    }
};
