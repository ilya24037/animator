<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use App\Models\Media;
use App\Http\Requests\StoreAnimatorRequest;
use App\Http\Requests\UpdateAnimatorRequest;
use App\Services\AnimatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AnimatorController extends Controller
{
    public function home(Request $request)
    {
        // ... ваш код из home (фильтрация, пагинация и др.) ...
        // Здесь можно оставить вашу логику без изменений!
        
        // For now, return a basic response - replace with your actual logic
        return Inertia::render('Animators/Home');
    }

    public function create()
    {
        $draft = AnimatorService::getUserDraft(Auth::id());
        return Inertia::render('Animators/Create', [
            'draft' => $draft ? AnimatorService::formatDraftData($draft) : null
        ]);
    }

    public function store(StoreAnimatorRequest $request)
    {
        $animator = AnimatorService::create($request->validated(), Auth::user());

        return redirect()->route(
            $animator->status === 'draft' ? 'profile.items' : 'profile.items',
            ['tab' => $animator->status === 'draft' ? 'draft' : 'pending']
        )->with('success', $animator->status === 'draft'
            ? 'Черновик сохранён'
            : 'Объявление отправлено на модерацию');
    }

    public function update(UpdateAnimatorRequest $request, Animator $animator)
    {
        $this->authorize('update', $animator);
        $animator = AnimatorService::update($animator, $request->validated(), Auth::user());

        return redirect()->route('profile.items', ['tab' => 'draft'])
            ->with('success', 'Изменения сохранены');
    }

    public function destroy(Animator $animator)
    {
        $this->authorize('delete', $animator);
        AnimatorService::destroy($animator);

        return redirect()->route('profile.items', ['tab' => 'draft'])
            ->with('success', 'Объявление удалено');
    }

    // AJAX методы для работы с черновиками
    public function getDraftAjax($id)
    {
        try {
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
            
            // Загружаем связанные данные
            $animator->load('media');
            
            // Форматируем для фронтенда
            $data = [
                'id' => $animator->id,
                'category_id' => $animator->category_id,
                'subcategory_id' => $animator->subcategory_id,
                'name' => $animator->title ?? $animator->name,
                'description' => $animator->description,
                'services' => $animator->services ?? [],
                'specialization' => $animator->specialization,
                'photos' => $animator->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => asset('storage/' . $media->path),
                        'type' => $media->type
                    ];
                }),
                'youtube_url' => $animator->youtube_url,
                'price' => $animator->price,
                'price_type' => $animator->price_type ?? 'fixed',
                'price_unit' => $animator->price_unit ?? 'service',
                'city_id' => $animator->city_id ?? $animator->city,
                'address' => $animator->address,
                'zones' => $animator->zones ?? 'city',
                'districts' => $animator->districts ?? [],
                'service_radius' => $animator->service_radius,
                'phone' => $animator->phone,
                'contact_name' => $animator->contact_name,
                'contact_methods' => $animator->contact_methods ?? ['phone', 'message'],
                'show_in_messages' => $animator->show_in_messages ?? true,
                'quick_booking' => $animator->quick_booking ?? false,
                'online_service' => $animator->online_service ?? false,
                'home_visit' => $animator->home_visit ?? true,
                'schedule' => $animator->schedule ?? null,
                'terms_accepted' => $animator->terms_accepted ?? false
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка загрузки черновика', [
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при загрузке черновика'
            ], 500);
        }
    }

    // Сохранение/обновление через AJAX
    public function storeAjax(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $this->prepareDataFromRequest($request);
            
            // Если есть ID, обновляем
            if ($request->has('id') && $request->id) {
                $animator = Animator::where('id', $request->id)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();
                    
                $animator->update($data);
            } else {
                // Создаем новый
                $data['user_id'] = auth()->id();
                $animator = Animator::create($data);
            }
            
            // Обработка фотографий
            if ($request->hasFile('photos')) {
                $this->handlePhotoUpload($request, $animator);
            }
            
            // Обработка существующих фото
            if ($request->has('existing_photos')) {
                $this->syncExistingPhotos($request->existing_photos, $animator);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $animator->id,
                    'status' => $animator->status
                ],
                'message' => $animator->status === 'draft' 
                    ? 'Черновик сохранен' 
                    : 'Объявление отправлено на модерацию'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Ошибка сохранения объявления', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении'
            ], 500);
        }
    }

    // Создание новой страницы Avito-style
    public function createAvito(Request $request)
    {
        // Проверяем, есть ли черновик
        $draft = null;
        if ($draftId = $request->get('draft')) {
            $draft = Animator::where('id', $draftId)
                ->where('user_id', auth()->id())
                ->where('status', 'draft')
                ->first();
        }
        
        return Inertia::render('Animators/CreateAvito', [
            'draftId' => $draft?->id,
            'categories' => $this->getCategories(),
            'cities' => $this->getCitiesWithIds()
        ]);
    }

    // Подготовка данных из запроса
    private function prepareDataFromRequest(Request $request)
    {
        $data = [
            'status' => $request->input('status', 'draft'),
            'category_id' => $request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id'),
            'title' => $request->input('name'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'specialization' => $request->input('specialization'),
            'price' => $request->input('price'),
            'price_type' => $request->input('price_type', 'fixed'),
            'price_unit' => $request->input('price_unit', 'service'),
            'city_id' => $request->input('city_id'),
            'city' => $this->getCityName($request->input('city_id')),
            'address' => $request->input('address'),
            'zones' => $request->input('zones', 'city'),
            'service_radius' => $request->input('service_radius'),
            'phone' => $request->input('phone'),
            'contact_name' => $request->input('contact_name'),
            'youtube_url' => $request->input('youtube_url'),
            'quick_booking' => $request->boolean('quick_booking'),
            'online_service' => $request->boolean('online_service'),
            'home_visit' => $request->boolean('home_visit'),
            'show_in_messages' => $request->boolean('show_in_messages'),
            'terms_accepted' => $request->boolean('terms_accepted'),
        ];
        
        // JSON поля
        if ($request->has('services')) {
            $data['services'] = $request->input('services');
        }
        
        if ($request->has('districts')) {
            $data['districts'] = $request->input('districts');
        }
        
        if ($request->has('contact_methods')) {
            $data['contact_methods'] = $request->input('contact_methods');
        }
        
        if ($request->has('schedule')) {
            $data['schedule'] = $request->input('schedule');
        }
        
        // Для совместимости со старой структурой
        $data['work_format'] = [
            'type' => 'private',
            'specialization' => $request->input('specialization'),
            'experience' => '1-3 года'
        ];
        
        $data['price_list'] = [
            'priceItems' => $request->input('services', [])
        ];
        
        $data['geo_data'] = [
            'city' => $data['city'],
            'address' => $data['address'],
            'zones' => $data['zones']
        ];
        
        $data['contacts_data'] = [
            'phone' => $data['phone'],
            'contact_name' => $data['contact_name'],
            'contact_methods' => $data['contact_methods'] ?? []
        ];
        
        return $data;
    }

    // Обработка загрузки фото
    private function handlePhotoUpload(Request $request, Animator $animator)
    {
        $photos = $request->file('photos');
        
        foreach ($photos as $index => $photo) {
            if (!$photo || !$photo->isValid()) continue;
            
            $path = $photo->store('animators/' . $animator->id, 'public');
            
            Media::create([
                'animator_id' => $animator->id,
                'path' => $path,
                'type' => 'photo',
                'uuid' => Str::uuid(),
                'order' => $index
            ]);
        }
    }

    // Синхронизация существующих фото
    private function syncExistingPhotos($photoIds, Animator $animator)
    {
        // Удаляем фото, которых нет в списке
        $animator->media()
            ->whereNotIn('id', $photoIds)
            ->delete();
        
        // Обновляем порядок
        foreach ($photoIds as $index => $photoId) {
            Media::where('id', $photoId)
                ->where('animator_id', $animator->id)
                ->update(['order' => $index]);
        }
    }

    // Получение имени города
    private function getCityName($cityId)
    {
        $cities = [
            1 => 'Москва',
            2 => 'Санкт-Петербург',
            3 => 'Казань',
            4 => 'Екатеринбург',
            5 => 'Новосибирск',
            6 => 'Краснодар',
            7 => 'Нижний Новгород',
            8 => 'Ростов-на-Дону',
            9 => 'Челябинск',
            10 => 'Пермь',
            11 => 'Самара'
        ];
        
        return $cities[$cityId] ?? 'Москва';
    }

    // Получение категорий с подкатегориями
    private function getCategories()
    {
        return [
            [
                'id' => 1,
                'name' => 'Красота и здоровье',
                'icon' => 'sparkles',
                'description' => 'Массаж, косметология, спа-процедуры',
                'count' => 1250,
                'is_popular' => true,
                'subcategories' => [
                    ['id' => 11, 'name' => 'Массаж', 'count' => 650],
                    ['id' => 12, 'name' => 'Косметология', 'count' => 320],
                    ['id' => 13, 'name' => 'Спа-процедуры', 'count' => 180],
                    ['id' => 14, 'name' => 'Маникюр и педикюр', 'count' => 100]
                ]
            ],
            [
                'id' => 2,
                'name' => 'Праздники и мероприятия',
                'icon' => 'music',
                'description' => 'Аниматоры, ведущие, организация праздников',
                'count' => 890,
                'is_popular' => true,
                'subcategories' => [
                    ['id' => 21, 'name' => 'Аниматоры', 'count' => 450],
                    ['id' => 22, 'name' => 'Ведущие', 'count' => 230],
                    ['id' => 23, 'name' => 'Фото и видео', 'count' => 120],
                    ['id' => 24, 'name' => 'Оформление', 'count' => 90]
                ]
            ],
            [
                'id' => 3,
                'name' => 'Обучение',
                'icon' => 'academic',
                'description' => 'Репетиторы, курсы, тренинги',
                'count' => 560,
                'is_popular' => false,
                'subcategories' => [
                    ['id' => 31, 'name' => 'Языки', 'count' => 280],
                    ['id' => 32, 'name' => 'Школьные предметы', 'count' => 180],
                    ['id' => 33, 'name' => 'Музыка', 'count' => 60],
                    ['id' => 34, 'name' => 'Спорт', 'count' => 40]
                ]
            ],
            [
                'id' => 4,
                'name' => 'Ремонт и строительство',
                'icon' => 'home',
                'description' => 'Мастера, сантехники, электрики',
                'count' => 780,
                'is_popular' => true,
                'subcategories' => [
                    ['id' => 41, 'name' => 'Комплексный ремонт', 'count' => 320],
                    ['id' => 42, 'name' => 'Сантехника', 'count' => 180],
                    ['id' => 43, 'name' => 'Электрика', 'count' => 140],
                    ['id' => 44, 'name' => 'Мелкий ремонт', 'count' => 140]
                ]
            ]
        ];
    }

    // Получение городов с ID
    private function getCitiesWithIds()
    {
        return [
            ['id' => 1, 'name' => 'Москва'],
            ['id' => 2, 'name' => 'Санкт-Петербург'],
            ['id' => 3, 'name' => 'Казань'],
            ['id' => 4, 'name' => 'Екатеринбург'],
            ['id' => 5, 'name' => 'Новосибирск'],
            ['id' => 6, 'name' => 'Краснодар'],
            ['id' => 7, 'name' => 'Нижний Новгород'],
            ['id' => 8, 'name' => 'Ростов-на-Дону'],
            ['id' => 9, 'name' => 'Челябинск'],
            ['id' => 10, 'name' => 'Пермь'],
            ['id' => 11, 'name' => 'Самара']
        ];
    }
}
