<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use App\Models\Media;
use App\Http\Requests\StoreAnimatorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AnimatorController extends Controller
{
    /**
     * Главная страница со списком аниматоров
     */
    public function home(Request $request)
    {
        // Получаем параметры фильтрации
        $city = $request->get('city', 'Москва');
        $type = $request->get('type');
        $isOnline = $request->boolean('is_online');
        $isVerified = $request->boolean('is_verified');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        
        // Базовый запрос
        $query = Animator::query()
            ->whereIn('status', ['active', 'published'])
            ->where('city', $city);
        
        // Применяем фильтры
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($isOnline) {
            $query->where('is_online', true);
        }
        
        if ($isVerified) {
            $query->where('is_verified', true);
        }
        
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        
        // Сортировка и пагинация
        $animators = $query
            ->orderByDesc('is_premium')
            ->orderByDesc('bumped_at')
            ->orderByDesc('created_at')
            ->paginate(20);
        
        // Преобразуем данные для фронтенда
        $cards = $animators->map(function ($animator) {
            return [
                'id' => $animator->id,
                'name' => $animator->name,
                'age' => $animator->age,
                'height' => $animator->height,
                'weight' => $animator->weight,
                'price' => $animator->price,
                'rating' => $animator->rating,
                'reviews' => $animator->reviews,
                'city' => $animator->city,
                'type' => $animator->type,
                'is_online' => $animator->is_online,
                'is_verified' => $animator->is_verified,
                'is_premium' => $animator->is_premium ?? false,
                'image' => $animator->image,
                'images' => $this->getAnimatorImages($animator),
            ];
        });
        
        return Inertia::render('Home', [
            'cards' => $cards,
            'filters' => [
                'city' => $city,
                'type' => $type,
                'is_online' => $isOnline,
                'is_verified' => $isVerified,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ],
            'cities' => $this->getCitiesList(),
            'pagination' => [
                'current_page' => $animators->currentPage(),
                'last_page' => $animators->lastPage(),
                'per_page' => $animators->perPage(),
                'total' => $animators->total(),
            ]
        ]);
    }

    /**
     * Форма создания нового объявления
     */
    public function create(Request $request)
    {
        // Проверяем, есть ли черновик
        $draft = Animator::where('user_id', auth()->id())
            ->where('status', 'draft')
            ->latest()
            ->first();
            
        return Inertia::render('Animators/Create', [
            'draft' => $draft ? $this->formatDraftData($draft) : null
        ]);
    }

    /**
     * Сохранение нового объявления
     */
    public function store(StoreAnimatorRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $this->prepareAnimatorData($request);
            $animator = Animator::create($data);
            
            // Обработка загруженных файлов
            if ($request->hasFile('media.files')) {
                $this->handleMediaUpload($request->file('media.files'), $animator);
            }
            
            DB::commit();
            
            // Редирект в зависимости от статуса
            if ($animator->status === 'draft') {
                return redirect()->route('profile.items', ['tab' => 'draft'])
                    ->with('success', 'Черновик сохранен');
            } else {
                return redirect()->route('profile.items', ['tab' => 'pending'])
                    ->with('success', 'Объявление отправлено на модерацию');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка создания объявления', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Произошла ошибка при сохранении'])
                        ->withInput();
        }
    }

    /**
     * Показ объявления
     */
    public function show(Animator $animator)
    {
        // Увеличиваем счетчик просмотров
        $animator->increment('views');
        
        return Inertia::render('Animators/Show', [
            'animator' => $this->formatAnimatorData($animator)
        ]);
    }

    /**
     * Форма редактирования
     */
    public function edit(Animator $animator)
    {
        // Проверка прав
        if ($animator->user_id !== auth()->id()) {
            abort(403);
        }
        
        return Inertia::render('Animators/Edit', [
            'animator' => $this->formatDraftData($animator)
        ]);
    }

    /**
     * Обновление объявления
     */
    public function update(StoreAnimatorRequest $request, Animator $animator)
    {
        // Проверка прав
        if ($animator->user_id !== auth()->id()) {
            abort(403);
        }
        
        try {
            DB::beginTransaction();
            
            $data = $this->prepareAnimatorData($request);
            $animator->update($data);
            
            // Обработка медиафайлов
            if ($request->hasFile('media.files')) {
                $this->handleMediaUpload($request->file('media.files'), $animator);
            }
            
            DB::commit();
            
            return redirect()->route('profile.items', ['tab' => 'draft'])
                ->with('success', 'Изменения сохранены');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка обновления объявления', [
                'animator_id' => $animator->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Произошла ошибка при сохранении']);
        }
    }

    /**
     * Удаление объявления
     */
    public function destroy(Animator $animator)
    {
        // Проверка прав
        if ($animator->user_id !== auth()->id()) {
            abort(403);
        }
        
        try {
            // Soft delete
            $animator->delete();
            
            return redirect()->route('profile.items', ['tab' => 'draft'])
                ->with('success', 'Объявление удалено');
                
        } catch (\Exception $e) {
            Log::error('Ошибка удаления объявления', [
                'animator_id' => $animator->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Не удалось удалить объявление']);
        }
    }

    /**
     * Получение черновика (AJAX)
     */
    public function getDraft($id)
    {
        $animator = Animator::where('id', $id)
            ->where('user_id', auth()->id())
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
            'animator' => $this->formatDraftData($animator)
        ]);
    }

    /**
     * Сохранение черновика (AJAX)
     */
    public function saveDraft(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $this->prepareAnimatorData($request);
            
            if ($draftId = $request->input('draft_id')) {
                $animator = Animator::where('id', $draftId)
                    ->where('user_id', auth()->id())
                    ->where('status', 'draft')
                    ->first();
                
                if ($animator) {
                    $animator->update($data);
                } else {
                    $animator = Animator::create($data);
                }
            } else {
                $animator = Animator::create($data);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'animator' => $animator,
                'message' => 'Черновик сохранен'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка сохранения черновика', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения'
            ], 500);
        }
    }

    /**
     * Публикация объявления (AJAX)
     */
    public function publish(Request $request)
    {
        try {
            $validator = $this->validateForPublish($request);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $data = $this->prepareAnimatorData($request);
            $data['status'] = 'pending'; // На модерацию
            
            if ($draftId = $request->input('draft_id')) {
                $animator = Animator::where('id', $draftId)
                    ->where('user_id', auth()->id())
                    ->first();
                    
                if ($animator) {
                    $animator->update($data);
                } else {
                    $animator = Animator::create($data);
                }
            } else {
                $animator = Animator::create($data);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Объявление отправлено на модерацию',
                'redirect' => route('profile.items', ['tab' => 'pending'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка публикации объявления', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при публикации'
            ], 500);
        }
    }
    
    /**
     * Подготовка данных для сохранения
     */
    private function prepareAnimatorData(Request $request): array
    {
        return [
            'user_id' => auth()->id(),
            'status' => $request->input('status', 'draft'),
            
            // Основные данные с значениями по умолчанию
            'title' => $request->input('details.title', ''),
            'name' => $request->input('details.title', ''),
            'description' => $request->input('details.description', ''),
            
            // JSON поля
            'work_format' => $request->input('workFormat', []),
            'price_list' => $request->input('priceList', ['priceItems' => []]),
            'actions_data' => $request->input('actions', []),
            'geo_data' => $request->input('geo', []),
            'contacts_data' => $request->input('contacts', []),
            
            // Обязательные поля с дефолтными значениями
            'type' => $request->input('workFormat.type', 'private'),
            'city' => $request->input('geo.city', 'Москва'),
            'price' => $request->input('price.value', 0),
            
            // Опциональные поля
            'address' => $request->input('geo.address', ''),
            'phone' => $request->input('contacts.phone', ''),
            'email' => $request->input('contacts.email', ''),
            'specialization' => $request->input('workFormat.specialization', ''),
            
            // Флаги
            'quick_booking' => false,
            'terms_accepted' => true,
        ];
    }

    /**
     * Форматирование данных черновика для фронтенда
     */
    private function formatDraftData(Animator $animator): array
    {
        return [
            'id' => $animator->id,
            'details' => [
                'title' => $animator->title,
                'description' => $animator->description
            ],
            'workFormat' => $animator->work_format ?? [],
            'priceList' => $animator->price_list ?? ['priceItems' => []],
            'price' => [
                'value' => $animator->price,
                'unit' => $animator->price_list['unit'] ?? 'за час',
                'isBasePrice' => false
            ],
            'actions' => $animator->actions_data ?? [],
            'media' => [
                'files' => [],
                'videoUrl' => ''
            ],
            'geo' => $animator->geo_data ?? [
                'city' => $animator->city,
                'address' => $animator->address,
                'visitType' => 'all_city'
            ],
            'contacts' => $animator->contacts_data ?? [
                'phone' => $animator->phone,
                'email' => $animator->email,
                'contactWays' => ['any']
            ]
        ];
    }

    /**
     * Форматирование данных для отображения
     */
    private function formatAnimatorData(Animator $animator): array
    {
        return [
            'id' => $animator->id,
            'title' => $animator->title,
            'description' => $animator->description,
            'price' => $animator->price,
            'city' => $animator->city,
            'rating' => $animator->rating,
            'reviews' => $animator->reviews,
            'is_online' => $animator->is_online,
            'is_verified' => $animator->is_verified,
            'work_format' => $animator->work_format,
            'price_list' => $animator->price_list,
            'geo_data' => $animator->geo_data,
            'contacts_data' => $animator->contacts_data,
            'images' => $this->getAnimatorImages($animator),
            'created_at' => $animator->created_at,
            'views' => $animator->views ?? 0
        ];
    }

    /**
     * Получить список изображений аниматора
     */
    private function getAnimatorImages($animator)
    {
        // Если есть связанные медиафайлы
        if (method_exists($animator, 'media') && $animator->media && $animator->media->count() > 0) {
            return $animator->media->map(function ($media) {
                return asset('storage/' . $media->path);
            })->toArray();
        }
        
        // Если есть старое поле image
        if ($animator->image && $animator->image !== 'default.jpg') {
            return [asset('storage/animators/' . $animator->image)];
        }
        
        // Возвращаем пустой массив
        return [];
    }
    
    /**
     * Получить список городов
     */
    private function getCitiesList()
    {
        return [
            'Москва',
            'Санкт-Петербург', 
            'Казань',
            'Екатеринбург',
            'Новосибирск',
            'Краснодар',
            'Нижний Новгород',
            'Ростов-на-Дону',
            'Челябинск',
            'Пермь',
            'Самара'
        ];
    }

    /**
     * Обработка загрузки медиафайлов
     */
    private function handleMediaUpload($files, Animator $animator)
    {
        foreach ($files as $file) {
            $path = $file->store('animators/' . $animator->id, 'public');
            
            Media::create([
                'animator_id' => $animator->id,
                'path' => $path,
                'type' => 'photo',
                'uuid' => Str::uuid()
            ]);
        }
    }

    /**
     * Валидация для публикации
     */
    private function validateForPublish(Request $request)
    {
        return validator($request->all(), [
            'details.title' => 'required|string|min:3|max:255',
            'details.description' => 'required|string|min:10',
            'workFormat.type' => 'required|string',
            'workFormat.specialization' => 'required|string',
            'price.value' => 'required|numeric|min:0',
            'geo.city' => 'required|string',
            'contacts.phone' => 'required|string'
        ], [
            'details.title.required' => 'Укажите название объявления',
            'details.description.required' => 'Добавьте описание',
            'workFormat.type.required' => 'Выберите формат работы',
            'workFormat.specialization.required' => 'Укажите специализацию',
            'price.value.required' => 'Укажите цену',
            'geo.city.required' => 'Выберите город',
            'contacts.phone.required' => 'Укажите номер телефона'
        ]);
    }
}