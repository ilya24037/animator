<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'price', 'city',
        'category', 'subcategory', 'age', 'height', 'weight',
        'rating', 'reviews', 'gender', 'image',
        'is_paid', 'published_at'
    ];
}
