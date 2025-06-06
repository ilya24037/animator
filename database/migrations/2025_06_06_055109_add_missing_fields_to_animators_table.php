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
            // Добавляем недостающие поля для формы создания объявления
            if (!Schema::hasColumn('animators', 'title')) {
                $table->string('title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('animators', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('animators', 'address')) {
                $table->string('address')->nullable()->after('city');
            }
            if (!Schema::hasColumn('animators', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
            if (!Schema::hasColumn('animators', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('animators', 'specialization')) {
                $table->string('specialization')->nullable()->after('email');
            }
            if (!Schema::hasColumn('animators', 'work_format')) {
                $table->json('work_format')->nullable()->after('specialization');
            }
            if (!Schema::hasColumn('animators', 'price_list')) {
                $table->json('price_list')->nullable()->after('work_format');
            }
            if (!Schema::hasColumn('animators', 'actions_data')) {
                $table->json('actions_data')->nullable()->after('price_list');
            }
            if (!Schema::hasColumn('animators', 'geo_data')) {
                $table->json('geo_data')->nullable()->after('actions_data');
            }
            if (!Schema::hasColumn('animators', 'contacts_data')) {
                $table->json('contacts_data')->nullable()->after('geo_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'description', 'address', 'phone', 'email', 
                'specialization', 'work_format', 'price_list', 
                'actions_data', 'geo_data', 'contacts_data'
            ]);
        });
    }
};