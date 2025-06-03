<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController;

// ✅ API маршрут для получения списка категорий
Route::get('/categories', [CategoryApiController::class, 'index']);
