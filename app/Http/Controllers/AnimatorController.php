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
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'nullable|numeric|min:0',
            'city'        => 'nullable|string|max:255',
            'status'      => 'required|string|in:draft,pending,published',
        ]);

        $animator = Animator::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? '',
            'price'       => $validated['price'] ?? null,
            'city'        => $validated['city'] ?? '',
            'status'      => $validated['status'],
        ]);

        return redirect()->route('profile.items')->with('success', 'Анкета сохранена');
    }
}
