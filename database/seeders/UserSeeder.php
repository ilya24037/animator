<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ► Дев-учётка c предсказуемым паролем
        User::firstOrCreate(
            ['email' => 'sveta@mail.ru'],
            [
                'name'              => 'Sveta Dev',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // ► Остальные случайные пользователи (как раньше)
        User::factory()->count(9)->create();
    }
}
