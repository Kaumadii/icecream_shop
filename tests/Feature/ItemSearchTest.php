<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_can_be_searched_by_name(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'cashier']));

        Item::factory()->create(['name' => 'Choco Fudge']);
        Item::factory()->create(['name' => 'Vanilla Cup']);

        $this->getJson('/api/items?search=choco')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Choco Fudge');
    }
}