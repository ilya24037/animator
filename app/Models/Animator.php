<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animator extends Model
{
    use HasFactory;

    protected $table = 'animators';

    // Разрешённые к массовому заполнению поля
    protected $fillable = [
        'name',
        'age',
        'height',
        'price',
        'rating',
        'reviews_count',
        'slug',
        'photo_folder',
        'status',
        'user_id',
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
