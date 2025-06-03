<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Animator; // ваш Eloquent-модель, где хранятся объявления

class DraftsController extends Controller
{
    /**
     * Отображает страницу «Мои объявления» (черновики и архив).
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Получаем черновики пользователя (предположим, что у модели Animator есть поле status: 'draft' или 'published')
        $drafts = Animator::query()
            ->where('user_id', $user->id)
            ->where('status', 'draft')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn($animator) => [
                'id'       => $animator->id,
                'title'    => $animator->title,
                'price'    => $animator->price
                                ? number_format($animator->price, 0, '.', ' ') . ' ₽'
                                : null,
                'lifetime' => 'Удалится навсегда через ' . now()
                                ->diffInDays($animator->updated_at->addDays(30))
                                . ' дней',
                'imageUrl' => $animator->media->first()?->getUrl() ?? null,
            ]);

        // Получаем архив (опубликованные или просроченные)
        $archive = Animator::query()
            ->where('user_id', $user->id)
            ->where('status', 'published')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn($animator) => [
                'id'       => $animator->id,
                'title'    => $animator->title,
                'price'    => $animator->price
                                ? number_format($animator->price, 0, '.', ' ') . ' ₽'
                                : null,
                'lifetime' => 'Удалится навсегда через ' . now()
                                ->diffInDays($animator->expires_at)
                                . ' дней',
                'imageUrl' => $animator->media->first()?->getUrl() ?? null,
            ]);

        return Inertia::render('Personal/Drafts', [
            'drafts'  => $drafts,
            'archive' => $archive,
        ]);
    }
}
