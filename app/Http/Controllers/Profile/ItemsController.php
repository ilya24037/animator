<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', Item::STATUS_WAITING);

        $items = Item::where('user_id', $request->user()->id)
            ->status($status)
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        // базовый подсчёт по статусам
        $counts = Item::selectRaw('status, count(*) as total')
            ->where('user_id', $request->user()->id)
            ->groupBy('status')
            ->pluck('total', 'status');

        // гарантируем, что у каждого статуса есть ключ (иначе undefined)
        $counts = collect($counts)->union([
            Item::STATUS_WAITING  => 0,
            Item::STATUS_DRAFT    => 0,
            Item::STATUS_ARCHIVED => 0,
        ]);

        return Inertia::render('Profile/Items', [
            'items'         => $items,
            'counts'        => $counts,
            'currentStatus' => $status,
        ]);
    }

    public function updateStatus(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $request->validate([
            'status' => 'required|in:draft,waiting,archived,active',
        ]);

        $item->update(['status' => $request->input('status')]);

        return back()->with('flash', ['message' => 'Статус объявления обновлён!']);
    }
}
