<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_all_categories(): void
    {
        Category::factory()->create(['name' => 'Аниматор']);
        Category::factory()->create(['name' => 'Клоун']);

        $response = $this->getJson('/api/categories');

        $response->assertOk()
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['name' => 'Аниматор'])
                 ->assertJsonFragment(['name' => 'Клоун']);
    }
}
