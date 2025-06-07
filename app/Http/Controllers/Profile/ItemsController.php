<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Animator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function index(Request $request, $tab = 'draft', $filter = 'all')
    {
        $userId = Auth::id();

        // Карта вкладок <-> статусов для таблицы items
        $itemStatusMap = [
            'draft'     => 'draft',
            'published' => 'active',
            'inactive'  => 'archived',
            'old'       => 'archived',
        ];

        // Карта вкладок <-> статусов для таблицы animators
        $animatorStatusMap = [
            'draft'     => 'draft',
            'pending'   => 'pending',
            'published' => ['published', 'active'],
            'inactive'  => 'inactive',
            'old'       => 'archived',
        ];

        // Получаем данные из таблицы items
        $itemStatus = $itemStatusMap[$tab] ?? 'draft';
        $items = Item::where('user_id', $userId)
            ->where('status', $itemStatus)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'price' => $item->price ? number_format($item->price, 0, '.', ' ') . ' ₽' : null,
                    'status' => $item->status,
                    'created_at' => $item->created_at?->format('Y-m-d'),
                    'preview_url' => $item->preview_url,
                    'type' => 'item'
                ];
            });

        // Получаем данные из таблицы animators
        $animatorStatuses = $animatorStatusMap[$tab] ?? 'draft';
        $animatorQuery = Animator::where('user_id', $userId);
        
        if (is_array($animatorStatuses)) {
            $animatorQuery->whereIn('status', $animatorStatuses);
        } else {
            $animatorQuery->where('status', $animatorStatuses);
        }
        
        $animators = $animatorQuery->orderByDesc('created_at')
            ->get()
            ->map(function ($animator) {
                return [
                    'id' => $animator->id,
                    'title' => $animator->title ?? $animator->name ?? 'Без названия',
                    'price' => $animator->price ? number_format($animator->price, 0, '.', ' ') . ' ₽' : null,
                    'status' => $animator->status,
                    'created_at' => $animator->created_at?->format('Y-m-d'),
                    'preview_url' => $animator->image_url ?? null,
                    'type' => 'animator'
                ];
            });

        // Объединяем и сортируем по дате
        $allItems = $items->concat($animators)->sortByDesc('created_at')->values();

        // Подсчитываем количество для каждой вкладки
        $counts = [
            'draft'     => Item::where('user_id', $userId)->where('status', 'draft')->count() +
                          Animator::where('user_id', $userId)->where('status', 'draft')->count(),
            'pending'   => Animator::where('user_id', $userId)->where('status', 'pending')->count(),
            'published' => Item::where('user_id', $userId)->where('status', 'active')->count() +
                          Animator::where('user_id', $userId)->whereIn('status', ['published', 'active'])->count(),
            'inactive'  => Item::where('user_id', $userId)->where('status', 'archived')->count() +
                          Animator::where('user_id', $userId)->where('status', 'inactive')->count(),
            'old'       => Item::where('user_id', $userId)->where('status', 'archived')->count() +
                          Animator::where('user_id', $userId)->where('status', 'archived')->count(),
        ];

        return Inertia::render('Profile/Items', [
            'items'   => $allItems,
            'tab'     => $tab,
            'filter'  => $filter,
            'counts'  => $counts,
            'query'   => $request->query(),
        ]);
    }
}
