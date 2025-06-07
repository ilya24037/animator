<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'name',
        'title',
        'description',
        'about',
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
        'address',
        'phone',
        'email',
        'specialization',
        'work_format',
        'price_list',
        'actions_data',
        'geo_data',
        'contacts_data',
        'type',
        'is_online',
        'is_verified',
        'is_premium',        // ← Добавлено
        'image',
        'status',
        'zones',
        'services',
        'heroes',
        'quick_booking',
        'terms_accepted',
        'bumped_at'          // ← Добавлено
    ];

    protected $casts = [
        'work_format' => 'array',
        'price_list' => 'array',
        'actions_data' => 'array',
        'geo_data' => 'array',
        'contacts_data' => 'array',
        'services' => 'array',
        'is_online' => 'boolean',
        'is_verified' => 'boolean',
        'is_premium' => 'boolean',       // ← Добавлено
        'quick_booking' => 'boolean',
        'terms_accepted' => 'boolean',
        'rating' => 'float',
        'price' => 'decimal:2',
        'bumped_at' => 'datetime'        // ← Добавлено
    ];

    /**
     * Отношение к пользователю
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отношение к медиафайлам
     */
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Получить фотографии
     */
    public function photos()
    {
        return $this->media()->where('type', 'photo');
    }

    /**
     * Scope для черновиков
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope для опубликованных
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope для активных
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope для премиум объявлений
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope для пользователя
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для города
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Метод для поднятия объявления (bump)
     */
    public function bump()
    {
        $this->update(['bumped_at' => now()]);
    }
}