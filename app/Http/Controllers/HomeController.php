<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Animator;
use App\Models\Card;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Получаем город
        $city = $request->get('city', session('city', 'Москва'));
        
        // Строим запрос
        $query = Animator::where('status', 'active');
        
        // Применяем фильтры
        if ($city && $city !== 'Все города') {
            $query->where('city', $city);
        }
        
        if ($request->boolean('is_online')) {
            $query->where('is_online', true);
        }
        
        if ($request->boolean('is_verified')) {
            $query->where('is_verified', true);
        }
        
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }
        
        if ($minPrice = $request->get('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice = $request->get('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }
        
        // Получаем аниматоров
        $animators = $query->with('media')
                          ->orderBy('is_premium', 'desc')
                          ->orderBy('bumped_at', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);
        
        // Форматируем для отображения
        $cards = $animators->map(function ($animator) {
            return [
                'id' => $animator->id,
                'name' => $animator->name ?: $animator->title ?: 'Без имени',
                'price' => $animator->price ?: 0,
                'city' => $animator->city ?: 'Не указан',
                'age' => $animator->age,
                'height' => $animator->height,
                'weight' => $animator->weight,
                'rating' => $animator->rating ?: 4.5,
                'reviews' => $animator->reviews ?: 0,
                'image' => $animator->image ? asset('storage/' . $animator->image) : '/images/default-avatar.jpg',
                'images' => $animator->media()
                    ->where('type', 'photo')
                    ->pluck('path')
                    ->map(fn($path) => asset('storage/' . $path))
                    ->toArray(),
                'type' => $animator->type,
                'is_online' => $animator->is_online,
                'is_verified' => $animator->is_verified,
            ];
        });
        
        // Получаем список городов
        $cities = Animator::where('status', 'active')
                         ->whereNotNull('city')
                         ->distinct()
                         ->pluck('city')
                         ->filter()
                         ->sort()
                         ->values()
                         ->toArray();
        
        return Inertia::render('Home', [
            'cards' => $cards,
            'filters' => $request->only(['city', 'is_online', 'is_verified', 'type', 'min_price', 'max_price']),
            'cities' => $cities,
            'userCity' => $city,
        ]);
    }
}