<?php
// app/Models/Listing.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    // Типы объявлений
    const TYPE_SERVICE = 'service';      // Услуги (массаж, аниматоры и т.д.)
    const TYPE_PRODUCT = 'product';      // Товары
    const TYPE_REALTY = 'realty';        // Недвижимость
    
    // Статусы
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';     // На модерации
    const STATUS_ACTIVE = 'active';       // Опубликовано
    const STATUS_INACTIVE = 'inactive';   // Отклонено
    const STATUS_ARCHIVED = 'archived';   // В архиве

    protected $fillable = [
        // Основные поля
        'user_id',
        'type',
        'status',
        'title',
        'description',
        'price',
        'currency',
        'price_type', // за час, за услугу, за единицу
        
        // Категории
        'category_id',
        'subcategory_id',
        
        // Локация
        'city_id',
        'city',
        'address',
        'coordinates',
        'service_radius', // radius в км или "city", "district"
        
        // Контакты
        'phone',
        'email',
        'contact_methods', // ['phone', 'message', 'whatsapp']
        'contact_name',
        
        // Дополнительные данные (JSON)
        'attributes', // Специфичные для категории атрибуты
        'media',      // Фото/видео
        'schedule',   // График работы
        'services',   // Список услуг для сервисных объявлений
        
        // SEO
        'slug',
        'meta_title',
        'meta_description',
        
        // Статистика
        'views_count',
        'favorites_count',
        'messages_count',
        
        // Флаги
        'is_premium',
        'is_urgent',
        'is_highlighted',
        'quick_booking',
        'verified_at',
        
        // Даты
        'published_at',
        'expires_at',
        'bumped_at', // Последнее поднятие
    ];

    protected $casts = [
        'coordinates' => 'array',
        'contact_methods' => 'array',
        'attributes' => 'array',
        'media' => 'array',
        'schedule' => 'array',
        'services' => 'array',
        'price' => 'decimal:2',
        'is_premium' => 'boolean',
        'is_urgent' => 'boolean',
        'is_highlighted' => 'boolean',
        'quick_booking' => 'boolean',
        'verified_at' => 'datetime',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'bumped_at' => 'datetime',
    ];

    // Отношения
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('expires_at', '>', now());
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // Методы
    public function publish()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'published_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function archive()
    {
        $this->update(['status' => self::STATUS_ARCHIVED]);
    }

    public function bump()
    {
        $this->update(['bumped_at' => now()]);
        $this->touch(); // Обновляем updated_at
    }

    public function getRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function isFavoritedBy($user)
    {
        if (!$user) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    // Геттеры для обратной совместимости
    public function getNameAttribute()
    {
        return $this->title;
    }

    public function getIsOnlineAttribute()
    {
        return $this->attributes['attributes']['is_online'] ?? false;
    }

    public function getIsVerifiedAttribute()
    {
        return $this->verified_at !== null;
    }
}

// database/migrations/2025_06_10_create_listings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            
            // Основные поля
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('service')->index();
            $table->string('status')->default('draft')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable()->index();
            $table->string('currency', 3)->default('RUB');
            $table->string('price_type')->nullable();
            
            // Категории
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('subcategory_id')->nullable()->constrained();
            
            // Локация
            $table->unsignedBigInteger('city_id')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('address')->nullable();
            $table->json('coordinates')->nullable();
            $table->string('service_radius')->nullable();
            
            // Контакты
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('contact_methods')->nullable();
            $table->string('contact_name')->nullable();
            
            // JSON поля
            $table->json('attributes')->nullable();
            $table->json('media')->nullable();
            $table->json('schedule')->nullable();
            $table->json('services')->nullable();
            
            // SEO
            $table->string('slug')->unique()->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Статистика
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('messages_count')->default(0);
            
            // Флаги
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_highlighted')->default(false);
            $table->boolean('quick_booking')->default(false);
            $table->timestamp('verified_at')->nullable();
            
            // Даты
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('bumped_at')->nullable()->index();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Составные индексы
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['city_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('listings');
    }
};