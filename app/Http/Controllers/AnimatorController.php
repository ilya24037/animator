<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use App\Http\Requests\StoreAnimatorRequest;
use App\Http\Requests\UpdateAnimatorRequest;
use App\Services\AnimatorService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AnimatorController extends Controller
{
    public function home(Request $request)
    {
        // ... ваш код из home (фильтрация, пагинация и др.) ...
        // Здесь можно оставить вашу логику без изменений!
    }

    public function create()
    {
        $draft = AnimatorService::getUserDraft(Auth::id());
        return Inertia::render('Animators/Create', [
            'draft' => $draft ? AnimatorService::formatDraftData($draft) : null
        ]);
    }

    public function store(StoreAnimatorRequest $request)
    {
        $animator = AnimatorService::create($request->validated(), Auth::user());

        return redirect()->route(
            $animator->status === 'draft' ? 'profile.items' : 'profile.items',
            ['tab' => $animator->status === 'draft' ? 'draft' : 'pending']
        )->with('success', $animator->status === 'draft'
            ? 'Черновик сохранён'
            : 'Объявление отправлено на модерацию');
    }

    public function update(UpdateAnimatorRequest $request, Animator $animator)
    {
        $this->authorize('update', $animator);
        $animator = AnimatorService::update($animator, $request->validated(), Auth::user());

        return redirect()->route('profile.items', ['tab' => 'draft'])
            ->with('success', 'Изменения сохранены');
    }

    public function destroy(Animator $animator)
    {
        $this->authorize('delete', $animator);
        AnimatorService::destroy($animator);

        return redirect()->route('profile.items', ['tab' => 'draft'])
            ->with('success', 'Объявление удалено');
    }

    // Остальные методы: show, edit, ajax-методы — по аналогии
}
