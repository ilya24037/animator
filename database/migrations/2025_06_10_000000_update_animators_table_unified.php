<?php
// database/migrations/2025_06_10_000001_add_missing_fields_to_animators_simple.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Список полей, которые могут отсутствовать
        $fieldsToAdd = [
            'about' => ['text', 'nullable'],
            'zones' => ['string', 'default' => 'city'],
            'services' => ['json', 'nullable'],
            'heroes' => ['text', 'nullable'],
            'quick_booking' => ['boolean', 'default' => false],
            'terms_accepted' => ['boolean', 'default' => false],
            'city_id' => ['unsignedBigInteger', 'default' => 1],
        ];

        Schema::table('animators', function (Blueprint $table) use ($fieldsToAdd) {
            foreach ($fieldsToAdd as $fieldName => $fieldConfig) {
                if (!Schema::hasColumn('animators', $fieldName)) {
                    $type = $fieldConfig[0];
                    $column = $table->$type($fieldName);
                    
                    // Добавляем модификаторы
                    if (isset($fieldConfig['nullable'])) {
                        $column->nullable();
                    }
                    if (isset($fieldConfig['default'])) {
                        $column->default($fieldConfig['default']);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('animators', function (Blueprint $table) {
            $fields = ['about', 'zones', 'services', 'heroes', 'quick_booking', 'terms_accepted', 'city_id'];
            
            foreach ($fields as $field) {
                if (Schema::hasColumn('animators', $field)) {
                    $table->dropColumn($field);
                }
            }
        });
    }
};