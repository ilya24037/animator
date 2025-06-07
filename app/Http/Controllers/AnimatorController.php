<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use App\Http\Requests\StoreAnimatorRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AnimatorController extends Controller
{
    /**
     * Показать главную страницу
     */
    public function home(Request $request)
    {
        // Получаем аниматоров с фильтрацией
        $query = Animator::query();
        
        // Фильтруем по статусу (только опубликованные)
        $query->whereIn('status', ['published', 'active']);
        
        // Фильтры
        if ($request->city) {
            $query->where('city', $request->city);
        }
        
        if ($request->is_online) {
            $query->where('is_online', true);
        }
        
        if ($request->is_verified) {
            $query->where('is_verified', true);
        }
        
        if ($request->type && in_array($request->type, ['private', 'company'])) {
            $query->where('type', $request->type);
        }
        
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        try {
            $animators = $query->paginate(20);
            
            // Преобразуем в формат для карточек
            $cards = $animators->map(function ($animator) {
                return [
                    'id' => $animator->id,
                    'name' => $animator->name ?? $animator->title ?? 'Без названия',
                    'price' => $animator->price ?? 0,
                    'age' => $animator->age ?? null,
                    'height' => $animator->height ?? null,
                    'rating' => $animator->rating ?? 4.5,
                    'reviews' => $animator->reviews ?? 0,
                    'city' => $animator->city ?? 'Не указан',
                    'type' => $animator->type ?? 'private',
                    'images' => ['/images/placeholder.jpg'], // Упростим пока без связанных фото
                ];
            });
            
        } catch (\Exception $e) {
            Log::error('Error loading animators for home page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Возвращаем пустой результат в случае ошибки
            $cards = collect([]);
        }
        
        return Inertia::render('Home', [
            'cards' => $cards,
            'filters' => $request->only(['city', 'is_online', 'is_verified', 'type', 'min_price', 'max_price']),
            'cities' => ['Москва', 'Санкт-Петербург', 'Казань', 'Екатеринбург', 'Новосибирск', 'Пермь']
        ]);
    }

    /**
     * Показать форму создания объявления
     */
    public function create(Request $request)
    {
        // Проверяем есть ли черновик
        $draft = Animator::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->latest()
            ->first();
            
        return Inertia::render('Animators/Create', [
            'draftId' => $draft ? $draft->id : null
        ]);
    }

    /**
     * Сохранить объявление
     */
    public function store(StoreAnimatorRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            $status = $request->input('status', 'draft');
            
            // Подготавливаем данные
            $data = [
                'user_id' => $user->id,
                'title' => $request->input('details.title'),
                'name' => $request->input('details.title'), // Дублируем для совместимости
                'description' => $request->input('details.description'),
                'work_format' => $request->input('workFormat'),
                'price_list' => $request->input('priceList'),
                'price' => $request->input('price.value'),
                'actions_data' => $request->input('actions'),
                'geo_data' => $request->input('geo'),
                'contacts_data' => $request->input('contacts'),
                'status' => $status,
                'city' => $request->input('geo.city'),
                'address' => $request->input('geo.address'),
                'phone' => $request->input('contacts.phone'),
                'email' => $request->input('contacts.email'),
                'specialization' => $request->input('workFormat.specialization'),
                'type' => $request->input('workFormat.type', 'private'),
                'quick_booking' => false,
                'terms_accepted' => true,
            ];
            
            $animator = Animator::create($data);
            
            // Обработка медиафайлов
            if ($request->has('media.files') && is_array($request->input('media.files'))) {
                foreach ($request->input('media.files') as $file) {
                    if (isset($file['preview'])) {
                        // Здесь должна быть логика сохранения файлов
                        // Пока просто логируем
                        Log::info('Media file received', ['file' => $file]);
                    }
                }
            }
            
            DB::commit();
            
            $message = $status === 'draft' 
                ? 'Черновик успешно сохранен!' 
                : 'Объявление отправлено на модерацию!';
            
            if ($status === 'draft') {
                return redirect()->route('profile.items', ['tab' => 'draft', 'filter' => 'all'])
                    ->with('success', $message);
            } else {
                return redirect()->route('dashboard')
                    ->with('success', $message);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating animator', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Произошла ошибка при сохранении. Попробуйте еще раз.');
        }
    }

    /**
     * Получить черновик по ID
     */
    public function getDraft($id)
    {
        $animator = Animator::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'draft')
            ->first();
            
        if (!$animator) {
            return response()->json([
                'success' => false,
                'message' => 'Черновик не найден'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'animator' => $animator
        ]);
    }

    /**
     * Сохранить черновик через AJAX
     */
    public function saveDraft(Request $request)
    {
        try {
            $user = Auth::user();
            $draftId = $request->input('draft_id');
            
            $data = [
                'user_id' => $user->id,
                'title' => $request->input('details.title'),
                'name' => $request->input('details.title'),
                'description' => $request->input('details.description'),
                'work_format' => $request->input('workFormat'),
                'price_list' => $request->input('priceList'),
                'price' => $request->input('price.value'),
                'actions_data' => $request->input('actions'),
                'geo_data' => $request->input('geo'),
                'contacts_data' => $request->input('contacts'),
                'status' => 'draft',
                'city' => $request->input('geo.city'),
                'address' => $request->input('geo.address'),
                'phone' => $request->input('contacts.phone'),
                'email' => $request->input('contacts.email'),
                'specialization' => $request->input('workFormat.specialization'),
                'type' => $request->input('workFormat.type', 'private'),
            ];
            
            if ($draftId) {
                // Обновляем существующий черновик
                $animator = Animator::where('id', $draftId)
                    ->where('user_id', $user->id)
                    ->where('status', 'draft')
                    ->first();
                    
                if ($animator) {
                    $animator->update($data);
                } else {
                    $animator = Animator::create($data);
                }
            } else {
                // Создаем новый черновик
                $animator = Animator::create($data);
            }
            
            return response()->json([
                'success' => true,
                'animator' => $animator,
                'message' => 'Черновик сохранен'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving draft', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении черновика'
            ], 500);
        }
    }

    /**
     * Показать объявление
     */
    public function show(Animator $animator)
    {
        if (!in_array($animator->status, ['published', 'active'])) {
            abort(404);
        }
        
        return Inertia::render('Animators/Show', [
            'animator' => $animator
        ]);
    }

    /**
     * Редактировать объявление
     */
    public function edit(Animator $animator)
    {
        // Проверяем права доступа
        if ($animator->user_id !== Auth::id()) {
            abort(403);
        }
        
        return Inertia::render('Animators/Edit', [
            'animator' => $animator
        ]);
    }

    /**
     * Обновить объявление
     */
    public function update(Request $request, Animator $animator)
    {
        // Проверяем права доступа
        if ($animator->user_id !== Auth::id()) {
            abort(403);
        }
        
        try {
            $data = [
                'title' => $request->input('details.title'),
                'name' => $request->input('details.title'),
                'description' => $request->input('details.description'),
                'work_format' => $request->input('workFormat'),
                'price_list' => $request->input('priceList'),
                'price' => $request->input('price.value'),
                'actions_data' => $request->input('actions'),
                'geo_data' => $request->input('geo'),
                'contacts_data' => $request->input('contacts'),
                'city' => $request->input('geo.city'),
                'address' => $request->input('geo.address'),
                'phone' => $request->input('contacts.phone'),
                'email' => $request->input('contacts.email'),
                'specialization' => $request->input('workFormat.specialization'),
                'type' => $request->input('workFormat.type', 'private'),
            ];
            
            $animator->update($data);
            
            return redirect()->route('profile.items', ['tab' => 'draft', 'filter' => 'all'])
                ->with('success', 'Объявление успешно обновлено!');
                
        } catch (\Exception $e) {
            Log::error('Error updating animator', [
                'animator_id' => $animator->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Произошла ошибка при обновлении.');
        }
    }

    /**
     * Удалить объявление
     */
    public function destroy(Animator $animator)
    {
        // Проверяем права доступа
        if ($animator->user_id !== Auth::id()) {
            abort(403);
        }
        
        try {
            $animator->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Объявление удалено'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting animator', [
                'animator_id' => $animator->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении'
            ], 500);
        }
    }
}