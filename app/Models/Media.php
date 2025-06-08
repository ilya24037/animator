<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Добавьте в fillable массив
protected $fillable = [
    'animator_id',
    'path',
    'type',
    'uuid',
    'order' // Добавить это поле
];

// Добавьте scope для сортировки
public function scopeOrdered($query)
{
    return $query->orderBy('order', 'asc');
}

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'animator_id',
        'path',
        'type',
        'uuid'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Отношение к аниматору
     */
    public function animator()
    {
        return $this->belongsTo(Animator::class);
    }

    /**
     * Получить полный URL файла
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Scope для фотографий
     */
    public function scopePhotos($query)
    {
        return $query->where('type', 'photo');
    }

    /**
     * Scope для видео
     */
    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }
}