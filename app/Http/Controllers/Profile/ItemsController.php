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
        $userId = $request->user()->id;

        $pending = Item::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $drafts = Item::where('user_id', $userId)
            ->where('status', 'draft')
            ->orderByDesc('created_at')
            ->get();

        $archive = Item::where('user_id', $userId)
            ->where('status', 'archive')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Profile/Items', [
            'pending' => $pending,
            'drafts' => $drafts,
            'archive' => $archive,
        ]);
    }
}
