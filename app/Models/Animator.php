<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animator extends Model
{
    use HasFactory;

    protected $table = 'animators';

    // ✅ ИСПРАВЛЕНО: Добавили все недостающие поля
    protected $fillable = [
        'user_id',
        'name',
        'title',           // Новое поле - название объявления
        'description',     // Новое поле - описание
        'age',
        'height',
        'weight',
        'price',
        'rating',
        'reviews_count',
        'reviews',
        'slug',
        'photo_folder',
        'city',
        'address',         // Новое поле - адрес
        'phone',           // Новое поле - телефон
        'email',           // Новое поле - email
        'specialization',  // Новое поле - специализация
        'work_format',     // JSON поле - формат работы
        'price_list',      // JSON поле - прайс-лист
        'actions_data',    // JSON поле - акции
        'geo_data',        // JSON поле - география
        'contacts_data',   // JSON поле - контакты
        'type',
        'is_online',
        'is_verified',
        'image',
        'status',
    ];

    // ✅ ДОБАВЛЕНО: Указываем Laravel как работать с JSON полями
    protected $casts = [
        'work_format' => 'array',
        'price_list' => 'array',
        'actions_data' => 'array',
        'geo_data' => 'array',
        'contacts_data' => 'array',
        'is_online' => 'boolean',
        'is_verified' => 'boolean',
        'rating' => 'float',
    ];

    /**
     * Отношение к пользователю (владелец объявления)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Остальной код модели без изменений
    public function getAllImageUrlsAttribute()
    {
        $images = [];

        for ($i = 1; $i <= 20; $i++) {
            $path = public_path("images/cards/{$this->photo_folder}/{$i}.jpg");
            if (file_exists($path)) {
                $images[] = asset("images/cards/{$this->photo_folder}/{$i}.jpg");
            }
        }

        return $images;
    }

    public function getMainImageUrlAttribute()
    {
        return asset("images/cards/{$this->photo_folder}/main.jpg");
    }
}