<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AnimatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimatorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_animator()
    {
        $user = User::factory()->create();
        $data = [
            'title' => 'Тестовое объявление',
            'status' => 'draft',
            // ... другие нужные поля ...
        ];

        $animator = AnimatorService::create($data, $user);

        $this->assertNotNull($animator->id);
        $this->assertEquals($user->id, $animator->user_id);
        $this->assertEquals('draft', $animator->status);
    }
}
