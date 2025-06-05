<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Применяем фильтры по параметрам запроса (как было)
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

        // Получаем карточки и собираем пути к картинкам (как было)
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
        // На выходе будем передавать flash-сообщение successMessage (если оно есть)
        return Inertia::render('Animators/Create', [
            // Если с прошлого запроса в сессии лежит 'success', Inertia отдаст его во Vue
            'successMessage' => session('success'),
        ]);
    }

    /**
     * Сохранить новое объявление (либо опубликовать, либо сохранить как черновик)
     */
    public function store(Request $request)
    {
        /*
         * 1) Поскольку форма отправляет вложенный массив:
         *    - form.details.title
         *    - form.details.description
         *    - form.price.value
         *    - form.geo.city
         *    - form.status
         *
         *   то валифицируем именно dot-нотацией:
         */
        $validated = $request->validate([
            'details.title'       => 'required|string|max:255',
            'details.description' => 'nullable|string',
            'price.value'         => 'nullable|numeric|min:0',
            'geo.city'            => 'nullable|string|max:255',
            'status'              => 'required|string|in:draft,pending,published',
        ]);

        // Сохраняем аниматора, вытаскивая данные из вложенных массивов:
        $animator = Animator::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['details']['title'],
            'description' => $validated['details']['description'] ?? '',
            'price'       => $validated['price']['value'] ?? null,
            'city'        => $validated['geo']['city'] ?? '',
            'status'      => $validated['status'],
        ]);

        /*
         * 2) После того, как удалось сохранить или опубликовать, 
         *    мы делаем redirect на ту вкладку, что соответствует status:
         *    - draft   → /profile/items/draft/all
         *    - pending → /profile/items/pending/all
         *    - published → /profile/items/published/all
         *
         *  Пример маршрута (в web.php) мы уже сделали с параметром {tab}/{filter?}.
         */
        return redirect()->route('profile.items', [
            'tab'    => $validated['status'],
            'filter' => 'all',
        ])->with('success', 'Анкета сохранена');
    }
}
