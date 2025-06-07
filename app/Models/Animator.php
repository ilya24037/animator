<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animator extends Model
{
    use HasFactory, SoftDeletes;

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
        'image',
        'status',
        'zones',
        'services',
        'heroes',
        'quick_booking',
        'terms_accepted'
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
        'quick_booking' => 'boolean',
        'terms_accepted' => 'boolean',
        'rating' => 'float',
        'price' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
        'type' => 'private',
        'is_online' => false,
        'is_verified' => false,
        'quick_booking' => false,
        'terms_accepted' => false,
        'rating' => 4.5,
        'reviews' => 0,
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
     * Scope для пользователя
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope по статусу
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope по городу
     */
    public function scopeCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope по типу
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope для онлайн
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    /**
     * Scope для проверенных
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Accessor для получения полного URL изображения
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholder.jpg');
    }

    /**
     * Accessor для получения форматированной цены
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->price) {
            return number_format($this->price, 0, '.', ' ') . ' ₽';
        }
        return 'Цена договорная';
    }

    /**
     * Проверка, является ли объявление черновиком
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Проверка, опубликовано ли объявление
     */
    public function isPublished()
    {
        return in_array($this->status, ['published', 'active']);
    }

    /**
     * Проверка принадлежности пользователю
     */
    public function belongsToUser($userId)
    {
        return $this->user_id == $userId;
    }
}