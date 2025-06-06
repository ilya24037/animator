<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnimatorController extends Controller
{
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
    
    public function saveDraft(Request $request)
    {
        // Этот метод можно удалить, так как теперь используем store/update
        return $this->store($request);
    }
}