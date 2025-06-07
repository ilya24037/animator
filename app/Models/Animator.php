<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animator extends Model
{
    use HasFactory;   // ← синтаксис приведён в порядок

    /**
     * Атрибуты, которые можно массово заполнять.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'city_id',
        'name',
        'title',
        'description',
        'about',
        'age',
        'height',
        'price',
        'experience',
        'phone',
        'telegram',
        'whatsapp',
        'vk',
        'instagram',
        'avatar',
        'slug',
        'photo_folder',
        'city',
        'address',
        'email',
        'specialization',
        'work_format',
        'price_list',
        'actions_data',
        'geo_data',
        'contacts_data',
        'type',
        'rating',
        'reviews',
        'status',
        'is_online',
        'is_verified',
        'image',
        'zones',
        'services',
        'heroes',
        'quick_booking',
        'terms_accepted'
    ];

    /**
     * Приведение отдельных полей к нужному типу.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'work_format'     => 'array',
        'price_list'      => 'array',
        'actions_data'    => 'array',
        'geo_data'        => 'array',
        'contacts_data'   => 'array',
        'is_online'       => 'boolean',
        'is_verified'     => 'boolean',
        'quick_booking'   => 'boolean',
        'terms_accepted'  => 'boolean',
        'rating'          => 'float',
        'reviews'         => 'integer',
    ];

    /**
     * Значения полей по умолчанию.
     *
     * @var array<string,mixed>
     */
    protected $attributes = [
        'status'         => 'draft',
        'type'           => 'private',
        'is_online'      => false,
        'is_verified'    => false,
        'quick_booking'  => false,
        'terms_accepted' => false,
        'rating'         => 4.5,
        'reviews'        => 0,
    ];

    /* ───────────────────────────────────────────────
     |  Отношения Eloquent
     ─────────────────────────────────────────────── */

    /** Пользователь-владелец карточки */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Город, к которому относится аниматор */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /* ───────────────────────────────────────────────
     |  Локальные scopes
     ─────────────────────────────────────────────── */

    /** Scope: только опубликованные карточки */
    public function scopePublished($query)
    {
        return $query->whereIn('status', ['published', 'active']);
    }

    /** Scope: выборка для конкретного пользователя */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /* ───────────────────────────────────────────────
     |  Вспомогательные методы
     ─────────────────────────────────────────────── */

    /** Карточка сейчас в черновике? */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /** Карточка опубликована? */
    public function isPublished(): bool
    {
        return in_array($this->status, ['published', 'active']);
    }

    /** Принадлежит ли карточка указанному пользователю? */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }
}
