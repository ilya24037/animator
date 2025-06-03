<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- подключаем Auth

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

            if (File::exists($imageDir)) {
                $main = "main.jpg";
                if (File::exists($imageDir . DIRECTORY_SEPARATOR . $main)) {
                    $images[] = asset("images/cards/{$card->id}/{$main}");
                }

                $files = collect(File::files($imageDir))
                    ->filter(fn($f) => preg_match('/\.(jpg|jpeg|png)$/i', $f->getFilename()) && $f->getFilename() !== 'main.jpg')
                    ->sortBy(fn($f) => $f->getFilename());

                foreach ($files as $file) {
                    $images[] = asset("images/cards/{$card->id}/" . $file->getFilename());
                }
            }

            // Если нет изображений – гарантируем, что всегда будет массив
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
     * Показать форму создания объявления (шаги Create.vue)
     */
    public function create()
    {
        return Inertia::render('Animators/Create');
    }

    /**
     * Сохранить новое объявление (либо опубликовать, либо сохранить как черновик)
     */
    public function store(Request $request)
    {
        // 1) Проверяем требуемые поля (без status и user_id, их будем вручную добавлять)
        $validated = $request->validate([
            'details.title'               => 'required|string|max:255',
            'details.description'         => 'required|string',
            'workFormat.specialization'   => 'required|string|max:255',
            'workFormat.type'             => 'required|string|max:50',
            'workFormat.clients'          => 'required|array|min:1',
            'workFormat.workFormats'      => 'required|array|min:1',
            'workFormat.serviceProviders' => 'required|array|min:1',
            'workFormat.experience'       => 'required|string',
            'price.value'                 => 'required|numeric|min:0',
            'actions.items'               => 'required|array|min:1',
            'geo.city'                    => 'required|string|max:255',
            'contacts.phone'              => 'required|string|max:20',
            'contacts.email'              => 'nullable|email',
            'contacts.method'             => 'nullable|string',
            'status'                      => 'required|string|in:pending,draft',
        ]);

        // 2) Собираем массив для создания записи: добавляем user_id и status
        $animatorData = [
            'name'        => $validated['details']['title'],
            'city'        => $validated['geo']['city'],
            'price'       => $validated['price']['value'],
            'type'        => $validated['workFormat']['type'],
            'is_online'   => false, // по умолчанию, или добавить логику, если в форме
            'is_verified' => false, // по умолчанию
            'age'         => null,  // можно расширить под вашу логику, если нужен возраст
            'height'      => null,
            'weight'      => null,
            'status'      => $validated['status'],       // статус из формы ("draft" или "pending")
            'user_id'     => $request->user()->id,       // текущий пользователь
        ];

        // 3) Создаём новый объект Animator
        $animator = Animator::create($animatorData);

        // 4) В зависимости от статуса – возвращаем пользователя с уведомлением
        if ($validated['status'] === 'draft') {
            return redirect()
                ->route('animators.create')
                ->with('success', 'Черновик сохранён!');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Объявление опубликовано!');
    }
}

