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


// Новая форма создания объявления Avito-style
Route::middleware('auth')->group(function () {
    // Страница создания
    Route::get('/create-ad', [AnimatorController::class, 'createAvito'])->name('create-ad');
    
    // API маршруты для AJAX
    Route::prefix('api')->group(function () {
        // Черновики
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

/* ─────────── Главная ─────────── */
Route::get('/', [HomeController::class, 'index'])->name('home');

/* ─────────── Dashboard ─────────── */
Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
      ->middleware(['auth', 'verified'])
      ->name('dashboard');

/* ─────────── Профиль + личные объявления ─────────── */
Route::middleware('auth')->group(function () {

    // Профиль пользователя
    Route::get   ('/profile',  [ProfileController::class, 'edit'   ])->name('profile.edit');
    Route::patch ('/profile',  [ProfileController::class, 'update' ])->name('profile.update');
    Route::delete('/profile',  [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Список объявлений с вкладками Avito-style
    Route::get('/profile/items/{tab}/{filter?}', [ItemsController::class, 'index'])
        ->where('tab', 'draft|pending|published|inactive|old')
        ->name('profile.items');

    // Короткий редирект для удобства
    Route::get('/profile/items', fn () =>
        redirect()->route('profile.items', ['tab' => 'draft', 'filter' => 'all'])
    );

    /* ─────────── Создание и управление объявлениями ─────────── */
    // Создание объявления (GET)
    Route::get ('/animators/create', [AnimatorController::class, 'create'])->name('animators.create');
    // Сохранение нового (POST)
    Route::post('/animators',        [AnimatorController::class, 'store' ])->name('animators.store');
    // Редактирование (GET)
    Route::get('/animators/{animator}/edit', [AnimatorController::class, 'edit'])->name('animators.edit');
    // Обновление (PUT)
    Route::put('/animators/{animator}', [AnimatorController::class, 'update'])->name('animators.update');
    // Удаление (DELETE)
    Route::delete('/animators/{animator}', [AnimatorController::class, 'destroy'])->name('animators.destroy');

    /* ─────────── AJAX маршруты для черновиков ─────────── */
    Route::get ('/animators/draft/{id}', [AnimatorController::class, 'getDraft'])->name('animators.getDraft');
    Route::post('/animators/draft',      [AnimatorController::class, 'saveDraft'])->name('animators.saveDraft');
    Route::post('/animators/publish',    [AnimatorController::class, 'publish'])->name('animators.publish');
});

// Публичные маршруты для объявлений
Route::get('/animators/{animator}', [AnimatorController::class, 'show'])->name('animators.show');

/* ─────────── Auth (Laravel Breeze) ─────────── */
Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::get ('/login',  [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login',  [AuthenticatedSessionController::class, 'store' ])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/forgot-password',  [PasswordResetLinkController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store' ])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password',       [NewPasswordController::class, 'store' ])->middleware('guest')->name('password.store');

Route::get('/verify-email',             EmailVerificationPromptController::class)->middleware('auth')->name('verification.notice');
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['auth','signed','throttle:6,1'])->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware(['auth','throttle:6,1'])->name('verification.send');

Route::get ('/confirm-password', [ConfirmablePasswordController::class, 'show' ])->middleware('auth')->name('password.confirm');
Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])->middleware('auth');
Route::put ('/password',         [PasswordController::class,          'update'])->middleware('auth')->name('password.update');

// require __DIR__.'/auth.php'; // Дублирует маршруты выше — не требуется
