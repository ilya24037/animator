<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnimatorController extends Controller
{
    public function home()
    {
        // Метод для главной страницы
        $animators = Animator::where('status', 'published')
            ->orWhere('status', 'active')
            ->paginate(20);
            
        return Inertia::render('Home', [
            'cards' => $animators
        ]);
    }

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
    
    public function getDraft($id)
    {
        $animator = Animator::where('id', $id)
            ->where('user_id', Auth::id())
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

    public function store(Request $request)
    {
        try {
            $isDraft = $request->boolean('is_draft', false);
            
            // Базовая валидация для черновика
            if (!$isDraft) {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'required|string',
                    'price' => 'required|numeric|min:0',
                    'services' => 'required|array|min:1',
                    'terms_accepted' => 'required|accepted'
                ]);
            }
            
            $data = [
                'user_id' => Auth::id(),
                'city_id' => $request->city_id ?? 1,
                'name' => $request->name,
                'description' => $request->description,
                'about' => $request->about,
                'price' => $request->price,
                'zones' => $request->zones ?? 'city',
                'services' => $request->services ? json_encode($request->services) : null,
                'heroes' => $request->heroes,
                'quick_booking' => $request->boolean('quick_booking', false),
                'terms_accepted' => $request->boolean('terms_accepted', false),
                'status' => $isDraft ? 'draft' : 'pending'
            ];
            
            $animator = Animator::create($data);
            
            // Обработка фотографий
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('animators', 'public');
                    $animator->media()->create([
                        'path' => $path,
                        'type' => 'photo'
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'animator' => $animator,
                'message' => $isDraft ? 'Черновик сохранен' : 'Объявление отправлено на модерацию'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating animator: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $animator = Animator::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
                
            $isDraft = $request->boolean('is_draft', false);
            
            // Валидация для публикации
            if (!$isDraft && $animator->status === 'draft') {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'required|string',
                    'price' => 'required|numeric|min:0',
                    'services' => 'required|array|min:1',
                    'terms_accepted' => 'required|accepted'
                ]);
            }
            
            $data = [
                'city_id' => $request->city_id ?? $animator->city_id,
                'name' => $request->name,
                'description' => $request->description,
                'about' => $request->about,
                'price' => $request->price,
                'zones' => $request->zones ?? 'city',
                'services' => $request->services ? json_encode($request->services) : $animator->services,
                'heroes' => $request->heroes,
                'quick_booking' => $request->boolean('quick_booking', false),
                'terms_accepted' => $request->boolean('terms_accepted', false),
                'status' => $request->status ?? ($isDraft ? 'draft' : 'pending')
            ];
            
            $animator->update($data);
            
            // Обработка новых фотографий
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('animators', 'public');
                    $animator->media()->create([
                        'path' => $path,
                        'type' => 'photo'
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'animator' => $animator,
                'message' => $isDraft ? 'Черновик обновлен' : 'Объявление обновлено'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating animator: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении'
            ], 500);
        }
    }
    
    /**
     * Сохранение черновика с преобразованием структуры данных
     */
    public function saveDraft(Request $request)
    {
        try {
            Log::info('Saving draft with data:', $request->all());
            
            // Преобразуем вложенную структуру формы в плоскую
            $data = $this->transformFormData($request->all());
            $data['user_id'] = Auth::id();
            $data['status'] = 'draft';
            
            // Если есть draft_id, обновляем
            if ($request->input('draft_id')) {
                $animator = Animator::where('id', $request->input('draft_id'))
                    ->where('user_id', Auth::id())
                    ->first();
                    
                if ($animator) {
                    $animator->update($data);
                } else {
                    $animator = Animator::create($data);
                }
            } else {
                $animator = Animator::create($data);
            }
            
            return response()->json([
                'success' => true,
                'animator' => $animator,
                'message' => 'Черновик сохранен'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving draft: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении черновика: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Преобразование вложенной структуры формы в плоскую для БД
     */
    private function transformFormData($formData)
    {
        $data = [];
        
        // Основные данные из details
        if (isset($formData['details'])) {
            $data['title'] = $formData['details']['title'] ?? '';
            $data['description'] = $formData['details']['description'] ?? '';
            // name используем из title если нет отдельного поля
            $data['name'] = $formData['details']['title'] ?? '';
        }
        
        // Формат работы
        if (isset($formData['workFormat'])) {
            $data['specialization'] = $formData['workFormat']['specialization'] ?? '';
            $data['type'] = $formData['workFormat']['type'] ?? 'private';
            $data['work_format'] = json_encode($formData['workFormat']);
        }
        
        // Прайс-лист
        if (isset($formData['priceList'])) {
            $data['price_list'] = json_encode($formData['priceList']);
        }
        
        // Цена
        if (isset($formData['price'])) {
            $data['price'] = $formData['price']['value'] ?? 0;
        }
        
        // Акции
        if (isset($formData['actions'])) {
            $data['actions_data'] = json_encode($formData['actions']);
        }
        
        // География
        if (isset($formData['geo'])) {
            $data['city'] = $formData['geo']['city'] ?? '';
            $data['address'] = $formData['geo']['address'] ?? '';
            $data['geo_data'] = json_encode($formData['geo']);
        }
        
        // Контакты
        if (isset($formData['contacts'])) {
            $data['phone'] = $formData['contacts']['phone'] ?? '';
            $data['email'] = $formData['contacts']['email'] ?? '';
            $data['contacts_data'] = json_encode($formData['contacts']);
        }
        
        // Дополнительные поля для совместимости с существующей структурой БД
        $data['age'] = $formData['age'] ?? null;
        $data['height'] = $formData['height'] ?? null;
        $data['weight'] = $formData['weight'] ?? null;
        $data['rating'] = $formData['rating'] ?? 0;
        $data['reviews'] = $formData['reviews'] ?? 0;
        $data['is_online'] = $formData['is_online'] ?? false;
        $data['is_verified'] = $formData['is_verified'] ?? false;
        
        return $data;
    }
}