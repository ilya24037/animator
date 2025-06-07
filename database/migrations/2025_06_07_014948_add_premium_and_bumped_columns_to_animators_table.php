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
            // Добавляем колонку is_premium если её ещё нет
            if (!Schema::hasColumn('animators', 'is_premium')) {
                $table->boolean('is_premium')->default(false)->after('is_verified');
            }
            
            // Добавляем колонку bumped_at если её ещё нет
            if (!Schema::hasColumn('animators', 'bumped_at')) {
                $table->timestamp('bumped_at')->nullable()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            // Удаляем колонки если они существуют
            if (Schema::hasColumn('animators', 'is_premium')) {
                $table->dropColumn('is_premium');
            }
            
            if (Schema::hasColumn('animators', 'bumped_at')) {
                $table->dropColumn('bumped_at');
            }
        });
    }
};