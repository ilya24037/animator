<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (!Schema::hasColumn('media', 'order')) {
                $table->unsignedInteger('order')->default(0)->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};