<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryApiController extends Controller
{
  public function index(): JsonResponse
{
    return response()->json([
        'success' => true,
        'data' => Category::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
    ]);
}
