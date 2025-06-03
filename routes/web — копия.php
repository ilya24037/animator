<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/* ──────────────────────────────────────────────────────────────
 |  Контроллеры приложения
 ────────────────────────────────────────────────────────────── */
use App\Http\Controllers\AnimatorController;
use App\Http\Controllers\ProfileController;

/* --- контроллер личного кабинета (объявления) --- */
use App\Http\Controllers\Profile\DraftsController;

/* --- auth (Laravel Breeze) --- */
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

/* ──────────────────────────────────────────────────────────────
 |  Публичные маршруты
 ────────────────────────────────────────────────────────────── */

/** Главная → Home.vue + список аниматоров */
Route::get('/', [AnimatorController::class, 'home'])->name('home');

/* ──────────────────────────────────────────────────────────────
 |  Создание объявления (требует auth)
 ────────────────────────────────────────────────────────────── */
Route::middleware('auth')->group(function () {
    Route::get('/animators/create',  [AnimatorController::class, 'create'])->name('animators.create');
    Route::post('/animators',        [AnimatorController::class, 'store' ])->name('animators.store');
});

/* ──────────────────────────────────────────────────────────────
 |  Dashboard (Личный кабинет — главная)
 ────────────────────────────────────────────────────────────── */
Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/* ──────────────────────────────────────────────────────────────
 |  Личный кабинет: разделы профиля
 ────────────────────────────────────────────────────────────── */
Route::middleware(['auth', 'verified'])
    ->prefix('profile')
    ->as('profile.')
    ->group(function () {
        // Главная страница личного кабинета → Dashboard.vue
        Route::get('/', fn () => Inertia::render('Dashboard'))->name('index');

        // Мои объявления (черновики / архив) → /profile/items
        Route::get('/items', [DraftsController::class, 'index'])->name('items');
    });

/* ──────────────────────────────────────────────────────────────
 |  Профиль пользователя (Laravel Breeze)
 ────────────────────────────────────────────────────────────── */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get   ('/profile/edit',   [ProfileController::class, 'edit'   ])->name('profile.edit');
    Route::patch ('/profile/edit',   [ProfileController::class, 'update' ])->name('profile.update');
    Route::delete('/profile/edit',   [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* ──────────────────────────────────────────────────────────────
 |  Auth-маршруты Breeze (регистрация, вход, сброс пароля)
 ────────────────────────────────────────────────────────────── */
Route::get ('/register',       [RegisteredUserController::class,      'create'])->middleware('guest')->name('register');
Route::post('/register',       [RegisteredUserController::class,      'store' ])->middleware('guest');
Route::get ('/login',          [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login',          [AuthenticatedSessionController::class, 'store' ])->middleware('guest');
Route::post('/logout',         [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get ('/forgot-password',[PasswordResetLinkController::class,   'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password',[PasswordResetLinkController::class,   'store' ])->middleware('guest')->name('password.email');
Route::get ('/reset-password/{token}', [NewPasswordController::class,      'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password',         [NewPasswordController::class,      'store' ])->middleware('guest')->name('password.update');

