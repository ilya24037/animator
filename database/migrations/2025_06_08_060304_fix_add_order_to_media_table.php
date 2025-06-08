<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Добавляем колонку type, если её нет
            if (!Schema::hasColumn('media', 'type')) {
                $table->string('type')->default('photo')->after('animator_id');
            }
            
            // Добавляем колонку order, если её нет
            if (!Schema::hasColumn('media', 'order')) {
                $table->integer('order')->default(0)->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Удаляем колонки при откате
            if (Schema::hasColumn('media', 'order')) {
                $table->dropColumn('order');
            }
            
            if (Schema::hasColumn('media', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};