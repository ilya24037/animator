namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ItemsController extends Controller
{
    public function index()
    {
        // Примерные данные, потом заменишь на Eloquent запросы из БД
        $pending = [
            [
                'id' => 1,
                'title' => 'Массаж спины',
                'price' => '3000 ₽ за сеанс',
                'lifetime' => 'Ожидает подтверждения',
                'imageUrl' => null,
                'editUrl' => '/additem?draftId=1'
            ],
        ];
        $drafts = [
            [
                'id' => 2,
                'title' => 'Детский праздник',
                'price' => null,
                'lifetime' => 'Удалится навсегда через 10 дней',
                'imageUrl' => null,
                'editUrl' => '/additem?draftId=2'
            ]
        ];
        $archive = [
            [
                'id' => 3,
                'title' => 'Новый год для малышей',
                'price' => 'Цена договорная',
                'lifetime' => 'Перемещён в архив',
                'imageUrl' => null,
                'editUrl' => '/additem?draftId=3'
            ]
        ];

        return Inertia::render('Personal/Items', [
            'pending' => $pending,
            'drafts'  => $drafts,
            'archive' => $archive,
        ]);
    }
}
