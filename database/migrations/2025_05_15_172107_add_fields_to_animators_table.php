<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAnimatorsTable extends Migration
{
    public function up(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $table->string('name');
            $table->integer('age')->nullable();
            $table->integer('height')->nullable();
            $table->integer('price')->nullable();
            $table->float('rating')->default(4.9);
            $table->integer('reviews_count')->default(0);
            $table->string('slug')->nullable();
            $table->string('photo_folder')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'age', 'height', 'price', 'rating',
                'reviews_count', 'slug', 'photo_folder'
            ]);
        });
    }
}
