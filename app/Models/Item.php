<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT    = 'draft';
    public const STATUS_WAITING  = 'waiting';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_ACTIVE   = 'active';

    protected $fillable = [
        'title',
        'price',
        'description',
        'status',
        // другие поля…
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /* -------------------- SCOPES -------------------- */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}