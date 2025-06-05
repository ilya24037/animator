<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemsController extends Controller
{
    public function index(Request $request, $tab = 'draft', $filter = 'all')
    {
        $userId = $request->user()->id;

        // Карта вкладок <-> статусов
        $statusMap = [
            'draft'     => 'draft',
            'published' => 'published',
            'inactive'  => 'inactive',
            'old'       => 'archive',
        ];
        $status = $statusMap[$tab] ?? 'draft';

        $items = Item::where('user_id', $userId)
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->get();

        $counts = [
            'draft'     => Item::where('user_id', $userId)->where('status', 'draft')->count(),
            'published' => Item::where('user_id', $userId)->where('status', 'published')->count(),
            'inactive'  => Item::where('user_id', $userId)->where('status', 'inactive')->count(),
            'old'       => Item::where('user_id', $userId)->where('status', 'archive')->count(),
        ];

        return Inertia::render('Profile/Items', [
            'items'   => $items,
            'tab'     => $tab,
            'filter'  => $filter,
            'counts'  => $counts,
            'query'   => $request->query(),
        ]);
    }
}
