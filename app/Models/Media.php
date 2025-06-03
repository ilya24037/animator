<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['animator_id', 'path', 'uuid'];

    public function animator()
    {
        return $this->belongsTo(Animator::class);
    }
}
