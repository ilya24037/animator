<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            if (!Schema::hasColumn('animators', 'is_online')) {
                $table->boolean('is_online')->default(false);
            }

            if (!Schema::hasColumn('animators', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }

            if (!Schema::hasColumn('animators', 'type')) {
                $table->string('type')->default('private');
            }
        });
    }

    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $table->dropColumn(['is_online', 'is_verified', 'type']);
        });
    }
};
