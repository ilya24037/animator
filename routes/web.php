<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/* ─────────── Контроллеры ─────────── */
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AnimatorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Profile\ItemsController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;

/* ─────────── Главная ─────────── */
Route::get('/', [HomeController::class, 'index'])->name('home');

/* ─────────── Dashboard ─────────── */
Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
      ->middleware(['auth', 'verified'])
      ->name('dashboard');

/* ─────────── Авторизованные маршруты ─────────── */
Route::middleware('auth')->group(function () {
    
    /* ─────────── Профиль пользователя ─────────── */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* ─────────── Список объявлений с вкладками ─────────── */
    Route::get('/profile/items/{tab}/{filter?}', [ItemsController::class, 'index'])
        ->where('tab', 'draft|pending|published|inactive|old')
        ->name('profile.items');

    // Короткий редирект для удобства
    Route::get('/profile/items', fn () =>
        redirect()->route('profile.items', ['tab' => 'draft', 'filter' => 'all'])
    );

    /* ─────────── Создание и управление объявлениями ─────────── */
    // Новая форма создания объявления Avito-style
    Route::get('/create-ad', [AnimatorController::class, 'createAvito'])->name('create-ad');
    
    // Старая форма создания (для обратной совместимости)
    Route::get('/animators/create', [AnimatorController::class, 'create'])->name('animators.create');
    
    // Сохранение нового (POST)
    Route::post('/animators', [AnimatorController::class, 'store'])->name('animators.store');
    
    // Редактирование (GET)
    Route::get('/animators/{animator}/edit', [AnimatorController::class, 'edit'])->name('animators.edit');
    
    // Обновление (PUT)
    Route::put('/animators/{animator}', [AnimatorController::class, 'update'])->name('animators.update');
    
    // Удаление (DELETE)
    Route::delete('/animators/{animator}', [AnimatorController::class, 'destroy'])->name('animators.destroy');

    /* ─────────── AJAX маршруты для черновиков ─────────── */
    Route::get('/animators/draft/{id}', [AnimatorController::class, 'getDraft'])->name('animators.getDraft');
    Route::post('/animators/draft', [AnimatorController::class, 'saveDraft'])->name('animators.saveDraft');
    Route::post('/animators/publish', [AnimatorController::class, 'publish'])->name('animators.publish');

    /* ─────────── API маршруты для AJAX ─────────── */
    Route::prefix('api')->group(function () {
        // Черновики (альтернативные пути для AJAX)
        Route::get('/animators/draft/{id}', [AnimatorController::class, 'getDraftAjax']);
        Route::post('/animators', [AnimatorController::class, 'storeAjax']);
        Route::put('/animators/{id}', [AnimatorController::class, 'storeAjax']);
        
        // Категории
        Route::get('/categories', function () {
            return response()->json(app(AnimatorController::class)->getCategories());
        });
        
        // Города
        Route::get('/cities', function () {
            return response()->json(app(AnimatorController::class)->getCitiesWithIds());
        });
    }); // Закрываем группу api
    
}); // Закрываем группу auth

/* ─────────── Публичные маршруты ─────────── */
// Просмотр объявления
Route::get('/animators/{animator}', [AnimatorController::class, 'show'])->name('animators.show');

/* ─────────── Auth (Laravel Breeze) ─────────── */
// Гостевые маршруты
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Маршруты требующие аутентификации
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});
