<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        // Применяем фильтры из запроса
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

        // Подготавливаем каждую карточку (коллекцию изображений)
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
        return Inertia::render('Animators/Create');
    }

    /**
     * Сохранить новое объявление (либо черновик, либо опубликовать)
     */
    public function store(Request $request)
    {
        //
        // Здесь мы ожидаем вложенные данные:
        // - details.title
        // - details.description
        // - price.value
        // - geo.city
        // - status
        //

        $validated = $request->validate([
            'details.title'       => 'required|string|max:255',
            'details.description' => 'nullable|string',
            'price.value'         => 'nullable|numeric|min:0',
            'geo.city'            => 'nullable|string|max:255',
            'status'              => 'nullable|string|in:draft,pending,published',
        ]);

        // Если статус не передали — по умолчанию 'draft'
        $status = $validated['status'] ?? 'draft';

        // Создаём новую запись, "распаковывая" вложенные значения
        $animator = Animator::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['details']['title'],
            'description' => $validated['details']['description'] ?? '',
            'price'       => $validated['price']['value'] ?? null,
            'city'        => $validated['geo']['city'] ?? '',
            'status'      => $status,
        ]);

        // После сохранения делаем редирект на вкладку с соответствующим статусом
        return redirect()->route('profile.items', [
            'tab'    => $status === 'draft' ? 'draft' :
                        ($status === 'pending' ? 'published' : 'published'),
            'filter' => 'all'
        ])->with('success', 'Анкета сохранена');
    }
}
