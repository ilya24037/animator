<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/* ─────────── Контроллеры ─────────── */
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
Route::get('/', [AnimatorController::class, 'home'])->name('home');

/* ─────────── Dashboard ─────────── */
Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
      ->middleware(['auth', 'verified'])
      ->name('dashboard');

/* ─────────── Профиль + личные объявления ─────────── */
Route::middleware('auth')->group(function () {

    /* профиль */
    Route::get   ('/profile',  [ProfileController::class, 'edit'   ])->name('profile.edit');
    Route::patch ('/profile',  [ProfileController::class, 'update' ])->name('profile.update');
    Route::delete('/profile',  [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* Новый маршрут вкладок объявлений (Avito-style) */
    Route::get('/profile/items/{tab}/{filter?}', [ItemsController::class, 'index'])
        ->where('tab', 'draft|published|inactive|old')
        ->name('profile.items');
});

/* ─────────── Auth (Breeze / Fortify) ─────────── */

/* регистрация */
Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

/* вход / выход */
Route::get ('/login',  [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login',  [AuthenticatedSessionController::class, 'store' ])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

/* сброс пароля (гости) */
Route::get('/forgot-password',  [PasswordResetLinkController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store' ])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password',       [NewPasswordController::class, 'store' ])->middleware('guest')->name('password.store');

/* подтверждение e-mail */
Route::get('/verify-email',             EmailVerificationPromptController::class)->middleware('auth')->name('verification.notice');
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['auth','signed','throttle:6,1'])->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware(['auth','throttle:6,1'])->name('verification.send');

/* подтверждение пароля + изменение пароля */
Route::get ('/confirm-password', [ConfirmablePasswordController::class, 'show' ])->middleware('auth')->name('password.confirm');
Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])->middleware('auth');
Route::put ('/password',         [PasswordController::class,          'update'])->middleware('auth')->name('password.update');

/* ─────────── Прочие маршруты (аниматоры, публичные) — оставлены без изменений ─────────── */

require __DIR__.'/auth.php';
