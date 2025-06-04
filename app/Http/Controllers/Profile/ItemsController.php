<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ItemsController extends Controller
{
    /**
     * Отображает страницу "Мои объявления"
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Загружаем объявления пользователя по статусам
        $pendingItems = Item::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();

        $draftItems = Item::where('user_id', $user->id)
            ->where('status', 'draft')
            ->get();

        $archivedItems = Item::where('user_id', $user->id)
            ->where('status', 'archived')
            ->get();

        // Передаём данные в компонент Vue (Profile/Items.vue)
        return Inertia::render('Profile/Items', [
            'pending' => $pendingItems,
            'drafts' => $draftItems,
            'archive' => $archivedItems,
        ]);
    }
}

