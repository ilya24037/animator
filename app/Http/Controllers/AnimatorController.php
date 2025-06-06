<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use App\Http\Requests\StoreAnimatorRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class AnimatorController extends Controller
{
    /**
     * Редирект с /animators → на главную
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * Главная страница с каталогом аниматоров (фильтры)
     */
    public function home()
    {
        $query = Animator::query();

        // Применяем фильтры по параметрам запроса
        if (request()->filled('city')) {
            $query->where('city', request('city'));
        }
        if (request()->filled('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }
        if (request()->filled('max_price')) {
            $query->where('price', '<=', request('max_price'));
        }
        if (request()->has('is_online')) {
            $query->where('is_online', filter_var(request('is_online'), FILTER_VALIDATE_BOOLEAN));
        }
        if (request()->has('is_verified')) {
            $query->where('is_verified', filter_var(request('is_verified'), FILTER_VALIDATE_BOOLEAN));
        }
        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        // Получаем все карточки и подготавливаем массив изображений для каждой
        $cards = $query->get()->map(function ($card) {
            $imageDir = public_path("images/cards/{$card->id}");
            $images = [];

            if (is_dir($imageDir)) {
                $main = "main.jpg";
                if (file_exists($imageDir . DIRECTORY_SEPARATOR . $main)) {
                    $images[] = asset("images/cards/{$card->id}/{$main}");
                }

                $files = collect(\Illuminate\Support\Facades\File::files($imageDir))
                    ->filter(fn($f) => preg_match('/\.(jpg|jpeg|png)$/i', $f->getFilename()) && $f->getFilename() !== 'main.jpg')
                    ->sortBy(fn($f) => $f->getFilename());

                foreach ($files as $file) {
                    $images[] = asset("images/cards/{$card->id}/" . $file->getFilename());
                }
            }

            $card->images = is_array($images) ? $images : [];
            return $card;
        });

        return Inertia::render('Home', [
            'cards'   => $cards,
            'filters' => request()->all(),
            'cities'  => Animator::select('city')->distinct()->pluck('city')->toArray(),
        ]);
    }

    /**
     * Показать форму создания объявления
     */
    public function create()
    {
        return Inertia::render('Animators/Create');
    }

    /**
     * ✅ ИСПРАВЛЕНО: Сохранить новое объявление (черновик или опубликовать)
     */
    public function store(Request $request)
    {
        // 📝 Логируем входящие данные для отладки
        Log::info('📥 Данные из формы создания объявления:', $request->all());

        try {
            // 🔍 Валидируем входящие данные с учетом вложенной структуры
            $validated = $request->validate([
                // Основные детали
                'details.title'       => 'required|string|max:255',
                'details.description' => 'nullable|string',
                
                // Формат работы
                'workFormat.specialization'   => 'nullable|string|max:255',
                'workFormat.type'             => 'nullable|string|max:100',
                'workFormat.clients'          => 'nullable|array',
                'workFormat.workFormats'      => 'nullable|array', 
                'workFormat.serviceProviders' => 'nullable|array',
                'workFormat.experience'       => 'nullable|string|max:100',
                
                // Прайс-лист
                'priceList.priceItems' => 'nullable|array',
                'priceList.priceItems.*.name' => 'string|max:255',
                'priceList.priceItems.*.price' => 'nullable|numeric|min:0',
                'priceList.priceItems.*.unit' => 'nullable|string|max:50',
                'priceList.priceItems.*.duration' => 'nullable|string|max:50',
                
                // Основная цена
                'price.value' => 'nullable|numeric|min:0',
                'price.unit'  => 'nullable|string|max:50',
                'price.isBasePrice' => 'nullable|boolean',
                
                // Акции
                'actions.discount' => 'nullable|numeric|min:0|max:100',
                'actions.gift'     => 'nullable|string|max:500',
                
                // География
                'geo.city'       => 'nullable|string|max:255',
                'geo.address'    => 'nullable|string|max:500',
                'geo.visitType'  => 'nullable|string|in:no_visit,all_city,zones',
                
                // Контакты
                'contacts.phone'       => 'nullable|string|max:20',
                'contacts.email'       => 'nullable|email|max:255',
                'contacts.contactWays' => 'nullable|array',
                
                // Обзор
                'review.text' => 'nullable|string',
                
                // Статус
                'status' => 'nullable|string|in:draft,pending,published',
            ]);

            Log::info('✅ Валидация прошла успешно:', $validated);

            // 📋 Если статус не передали — считаем это "draft"
            $status = $validated['status'] ?? 'draft';

            // 🔄 Преобразуем данные из формы в формат для базы данных
            $animatorData = [
                'user_id'     => Auth::id(),
                'title'       => $validated['details']['title'],
                'description' => $validated['details']['description'] ?? '',
                'price'       => $validated['price']['value'] ?? null,
                'city'        => $validated['geo']['city'] ?? '',
                'address'     => $validated['geo']['address'] ?? '',
                'phone'       => $validated['contacts']['phone'] ?? '',
                'email'       => $validated['contacts']['email'] ?? '',
                'specialization' => $validated['workFormat']['specialization'] ?? '',
                'status'      => $status,
                
                // 📦 Сохраняем сложные данные в JSON полях
                'work_format'  => $validated['workFormat'] ?? [],
                'price_list'   => $validated['priceList'] ?? [],
                'actions_data' => $validated['actions'] ?? [],
                'geo_data'     => $validated['geo'] ?? [],
                'contacts_data'=> $validated['contacts'] ?? [],
            ];

            Log::info('📤 Подготовленные данные для сохранения:', $animatorData);

            // 💾 Создаём запись в базе данных
            $animator = Animator::create($animatorData);

            Log::info('🎉 Аниматор успешно создан с ID: ' . $animator->id);

            // 📧 Сообщение об успешном сохранении
            $message = $status === 'draft' 
                ? 'Черновик успешно сохранен' 
                : 'Объявление успешно размещено';

            // 🔄 После сохранения перенаправляем пользователя на соответствующую вкладку
            return redirect()->route('profile.items', [
                'tab'    => $status === 'draft' ? 'draft' : 'published',
                'filter' => 'all'
            ])->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Ошибки валидации:', $e->errors());
            
            // Возвращаем ошибки валидации обратно в форму
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('💥 Ошибка при сохранении аниматора: ' . $e->getMessage());
            Log::error('🔍 Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Произошла ошибка при сохранении. Попробуйте еще раз.');
        }
    }
}