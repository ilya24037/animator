<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Получаем параметры фильтрации
        $city = $request->get('city', 'Москва');
        $type = $request->get('type');
        $isOnline = $request->boolean('is_online');
        $isVerified = $request->boolean('is_verified');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        
        // Базовый запрос
        $query = Animator::query()
            ->where('status', 'active')
            ->where('city', $city);
        
        // Применяем фильтры
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($isOnline) {
            $query->where('is_online', true);
        }
        
        if ($isVerified) {
            $query->where('is_verified', true);
        }
        
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        
        // Сортировка: сначала премиум, потом поднятые, потом новые
        $animators = $query
            ->orderByDesc('is_premium')
            ->orderByDesc('bumped_at')
            ->orderByDesc('created_at')
            ->paginate(20);
        
        // Преобразуем данные для фронтенда
        $cards = $animators->map(function ($animator) {
            return [
                'id' => $animator->id,
                'name' => $animator->name,
                'age' => $animator->age,
                'height' => $animator->height,
                'weight' => $animator->weight,
                'price' => $animator->price,
                'rating' => $animator->rating,
                'reviews' => $animator->reviews,
                'city' => $animator->city,
                'type' => $animator->type,
                'is_online' => $animator->is_online,
                'is_verified' => $animator->is_verified,
                'is_premium' => $animator->is_premium,
                'image' => $animator->image,
                'images' => $this->getAnimatorImages($animator),
            ];
        });
        
        return Inertia::render('Home', [
            'cards' => $cards,
            'filters' => [
                'city' => $city,
                'type' => $type,
                'is_online' => $isOnline,
                'is_verified' => $isVerified,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ],
            'cities' => $this->getCitiesList(),
            'pagination' => [
                'current_page' => $animators->currentPage(),
                'last_page' => $animators->lastPage(),
                'per_page' => $animators->perPage(),
                'total' => $animators->total(),
            ]
        ]);
    }
    
    /**
     * Получить список изображений аниматора
     */
    private function getAnimatorImages($animator)
    {
        // Если есть загруженные медиафайлы
        if ($animator->media && $animator->media->count() > 0) {
            return $animator->media->map(function ($media) {
                return asset('storage/' . $media->path);
            })->toArray();
        }
        
        // Если есть старое поле image
        if ($animator->image && $animator->image !== 'default.jpg') {
            return [asset('storage/animators/' . $animator->image)];
        }
        
        // Возвращаем пустой массив - компонент покажет заглушку
        return [];
    }
    
    /**
     * Получить список городов
     */
    private function getCitiesList()
    {
        return [
            'Москва',
            'Санкт-Петербург', 
            'Казань',
            'Екатеринбург',
            'Новосибирск',
            'Краснодар',
            'Нижний Новгород',
            'Ростов-на-Дону',
            'Челябинск',
            'Пермь',
            'Самара'
        ];
    }
}