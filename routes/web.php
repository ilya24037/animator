<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AnimatorController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// Главная страница → Home.vue + список аниматоров
Route::get('/', [AnimatorController::class, 'home'])->name('home');

// Защищённый маршрут для создания анкеты и публикации объявления
Route::middleware('auth')->group(function () {
    Route::get('/animators/create', [AnimatorController::class, 'create'])->name('animators.create');
    Route::post('/animators', [AnimatorController::class, 'store'])->name('animators.store'); // <-- Добавлено!
});

// Страница личного кабинета
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])
    ->prefix('profile')
    ->name('profile.')
    ->group(function () {
        Route::get('/items', [\App\Http\Controllers\Profile\ItemsController::class, 'index'])
            ->name('items');
        Route::patch('/items/{item}/status', [\App\Http\Controllers\Profile\ItemsController::class, 'updateStatus']);
    });


// --- Laravel Breeze маршруты --- //

// Регистрация
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// Вход
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

// Выход
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Забыли пароль
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// Сброс пароля
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');


// --- Для корректной работы профиля пользователя ---
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Profile\DraftsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршрут для «Мои объявления» (черновики и др.)
Route::middleware(['auth', 'verified'])
    ->prefix('profile')
    ->as('profile.')
    ->group(function () {
        Route::get('/items', [DraftsController::class, 'index'])->name('items');
    });
