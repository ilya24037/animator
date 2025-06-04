<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::inRandomOrder()->value('id') ?? User::factory(),
            'title'      => $this->faker->words(3, true),
            'description'=> $this->faker->paragraph(),
            'price'      => $this->faker->numberBetween(500, 5000),
            'city'       => $this->faker->city(),
            'status'     => Item::STATUS_DRAFT,  // по умолчанию черновик
            'preview_url'=> 'https://via.placeholder.com/150',
            'reason'     => null,
        ];
    }

    /**
     * Состояние «активное объявление»
     */
    public function active(): static
    {
        return $this->state(fn () => ['status' => Item::STATUS_ACTIVE]);
    }
}