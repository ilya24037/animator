<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Animator;
use App\Models\User;
use Faker\Factory as Faker;

class AnimatorSeeder extends Seeder
{
    /**
     * Города с их весами (процент объявлений)
     */
    private const CITIES_DISTRIBUTION = [
        'Москва' => 35,
        'Санкт-Петербург' => 20,
        'Казань' => 10,
        'Екатеринбург' => 10,
        'Новосибирск' => 8,
        'Нижний Новгород' => 7,
        'Пермь' => 5,
        'Ростов-на-Дону' => 5,
    ];

    /**
     * Специализации для генерации
     */
    private const SPECIALIZATIONS = [
        'Массаж' => [
            'классический',
            'спортивный',
            'расслабляющий',
            'антицеллюлитный',
            'лечебный',
            'тайский',
            'медовый',
            'лимфодренажный'
        ],
        'Спа-процедуры' => [
            'обертывания',
            'стоун-терапия',
            'ароматерапия',
            'талассотерапия'
        ],
        'Косметология' => [
            'уход за лицом',
            'чистка лица',
            'пилинг',
            'мезотерапия'
        ]
    ];

    /**
     * Количество записей для создания
     */
    private const TOTAL_RECORDS = 100;
    
    /**
     * Размер пакета для batch insert
     */
    private const BATCH_SIZE = 50;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Начинаем создание аниматоров...');
        
        // Используем транзакцию для быстрой вставки
        DB::beginTransaction();
        
        try {
            $faker = Faker::create('ru_RU');
            $totalCreated = 0;
            $animators = [];
            
            // Получаем или создаем тестового пользователя
            $user = $this->getOrCreateTestUser();
            
            // Генерируем аниматоров с учетом распределения по городам
            foreach (self::CITIES_DISTRIBUTION as $city => $percentage) {
                $countForCity = (int) ceil(self::TOTAL_RECORDS * $percentage / 100);
                
                for ($i = 0; $i < $countForCity; $i++) {
                    $animators[] = $this->generateAnimatorData($faker, $user->id, $city);
                    
                    // Batch insert для оптимизации
                    if (count($animators) >= self::BATCH_SIZE) {
                        Animator::insert($animators);
                        $totalCreated += count($animators);
                        $this->command->info("✓ Создано: {$totalCreated} записей");
                        $animators = [];
                    }
                }
            }
            
            // Вставляем оставшиеся записи
            if (!empty($animators)) {
                Animator::insert($animators);
                $totalCreated += count($animators);
            }
            
            DB::commit();
            
            $this->command->info("✅ Успешно создано {$totalCreated} аниматоров!");
            $this->logSummary($totalCreated);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Ошибка при создании аниматоров: ' . $e->getMessage());
            Log::error('AnimatorSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Получить или создать тестового пользователя
     */
    private function getOrCreateTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_verified' => true
            ]
        );
    }

    /**
     * Генерация данных для одного аниматора
     */
    private function generateAnimatorData($faker, int $userId, string $city): array
    {
        $specialization = $faker->randomElement(array_keys(self::SPECIALIZATIONS));
        $serviceType = $faker->randomElement(self::SPECIALIZATIONS[$specialization]);
        
        // Реалистичные цены в зависимости от города
        $basePrices = [
            'Москва' => 3000,
            'Санкт-Петербург' => 2500,
            'default' => 2000
        ];
        
        $basePrice = $basePrices[$city] ?? $basePrices['default'];
        $price = $faker->numberBetween($basePrice, $basePrice * 3);
        
        // Данные формы как в реальном приложении
        $workFormat = [
            'specialization' => $specialization,
            'type' => $faker->randomElement(['private', 'salon', 'chain']),
            'clients' => $faker->randomElements(['Женщины', 'Мужчины'], $faker->numberBetween(1, 2)),
            'workFormats' => $faker->randomElements([
                'У заказчика дома',
                'У себя дома',
                'В салоне',
                'В коворкинге',
                'В клинике'
            ], $faker->numberBetween(1, 3)),
            'serviceProviders' => $faker->randomElements(['Женщина', 'Мужчина'], 1),
            'experience' => $faker->randomElement(['Меньше года', '1–3 года', '4–7 лет', '8–10 лет', 'Больше 10 лет'])
        ];

        $priceList = [
            'priceItems' => $this->generatePriceItems($faker, $specialization, $basePrice)
        ];

        $geoData = [
            'city' => $city,
            'address' => $faker->streetAddress(),
            'visitType' => $faker->randomElement(['no_visit', 'all_city', 'zones'])
        ];

        $contactsData = [
            'phone' => $faker->phoneNumber(),
            'email' => $faker->optional(0.3)->safeEmail(),
            'contactWays' => $faker->randomElement(['any', 'phone', 'message'])
        ];

        $actionsData = [
            'discount' => $faker->optional(0.3)->numberBetween(5, 20),
            'gift' => $faker->optional(0.2)->sentence(3)
        ];

        return [
            'user_id' => $userId,
            'name' => $faker->name($faker->randomElement(['male', 'female'])),
            'title' => ucfirst($serviceType) . ' ' . $specialization,
            'description' => $this->generateDescription($faker, $specialization, $serviceType),
            'age' => $faker->numberBetween(23, 45),
            'height' => $faker->numberBetween(155, 190),
            'weight' => $faker->numberBetween(50, 90),
            'price' => $price,
            'rating' => $faker->randomFloat(1, 3.8, 5.0),
            'reviews' => $this->generateReviewsCount($faker),
            'city' => $city,
            'type' => $workFormat['type'] === 'private' ? 'private' : 'company',
            'image' => 'default.jpg',
            'status' => $this->generateStatus($faker),
            'is_online' => $faker->boolean(70),
            'is_verified' => $faker->boolean(30),
            'is_premium' => $faker->boolean(10),
            'specialization' => $specialization,
            'work_format' => json_encode($workFormat),
            'price_list' => json_encode($priceList),
            'geo_data' => json_encode($geoData),
            'contacts_data' => json_encode($contactsData),
            'actions_data' => json_encode($actionsData),
            'address' => $geoData['address'],
            'phone' => $contactsData['phone'],
            'email' => $contactsData['email'],
            'quick_booking' => $faker->boolean(40),
            'terms_accepted' => true,
            'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
            'bumped_at' => $faker->optional(0.3)->dateTimeBetween('-7 days', 'now')
        ];
    }

    /**
     * Генерация прайс-листа
     */
    private function generatePriceItems($faker, string $specialization, int $basePrice): array
    {
        $services = [
            'Массаж' => [
                'Классический массаж',
                'Антицеллюлитный массаж',
                'Лимфодренажный массаж',
                'Спортивный массаж',
                'Расслабляющий массаж'
            ],
            'Спа-процедуры' => [
                'Обертывание',
                'Стоун-терапия',
                'Ароматерапия',
                'Скрабирование'
            ],
            'Косметология' => [
                'Чистка лица',
                'Пилинг',
                'Массаж лица',
                'Маска для лица'
            ]
        ];

        $items = [];
        $availableServices = $services[$specialization] ?? $services['Массаж'];
        $selectedServices = $faker->randomElements($availableServices, $faker->numberBetween(3, 5));

        foreach ($selectedServices as $service) {
            $items[] = [
                'name' => $service,
                'price' => $faker->numberBetween($basePrice * 0.8, $basePrice * 1.5),
                'unit' => $faker->randomElement(['за услугу', 'за час', 'за сеанс']),
                'duration' => $faker->randomElement(['30 мин.', '45 мин.', '1 ч', '1 ч 30 мин.', '2 ч'])
            ];
        }

        return $items;
    }

    /**
     * Генерация описания
     */
    private function generateDescription($faker, string $specialization, string $serviceType): string
    {
        $templates = [
            'Профессиональный %s с опытом работы. %s',
            'Предлагаю качественные услуги %s. %s',
            'Сертифицированный специалист по %s. %s'
        ];

        $benefits = [
            'Индивидуальный подход к каждому клиенту.',
            'Использую только профессиональную косметику.',
            'Гарантирую результат после первого сеанса.',
            'Работаю по современным методикам.',
            'Имею медицинское образование.'
        ];

        $description = sprintf(
            $faker->randomElement($templates),
            mb_strtolower($serviceType),
            implode(' ', $faker->randomElements($benefits, 2))
        );

        return $description . ' ' . $faker->paragraph(2);
    }

    /**
     * Генерация количества отзывов с реалистичным распределением
     */
    private function generateReviewsCount($faker): int
    {
        $ranges = [
            [0, 10, 40],      // 40% - новички
            [11, 50, 30],     // 30% - со средним опытом
            [51, 150, 20],    // 20% - опытные
            [151, 500, 10]    // 10% - топовые
        ];

        $rand = $faker->numberBetween(1, 100);
        $cumulative = 0;

        foreach ($ranges as [$min, $max, $percentage]) {
            $cumulative += $percentage;
            if ($rand <= $cumulative) {
                return $faker->numberBetween($min, $max);
            }
        }

        return 0;
    }

    /**
     * Генерация статуса с реалистичным распределением
     */
    private function generateStatus($faker): string
    {
        $statuses = [
            'published' => 70,  // 70% опубликованы
            'active' => 15,     // 15% активны
            'draft' => 10,      // 10% черновики
            'pending' => 5      // 5% на модерации
        ];

        return $faker->randomElement(array_keys($statuses));
    }

    /**
     * Логирование итогов
     */
    private function logSummary(int $total): void
    {
        $summary = Animator::selectRaw('city, count(*) as count')
            ->groupBy('city')
            ->pluck('count', 'city');

        $this->command->table(
            ['Город', 'Количество'],
            $summary->map(fn($count, $city) => [$city, $count])->toArray()
        );

        Log::info('AnimatorSeeder completed', [
            'total' => $total,
            'distribution' => $summary->toArray()
        ]);
    }
}