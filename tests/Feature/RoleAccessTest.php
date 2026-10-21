<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function matrix(): array
    {
        $rules = [
            'items' => [
                'owner'   => ['view' => 200, 'create' => 201, 'update' => 200, 'delete' => 204],
                'manager' => ['view' => 200, 'create' => 201, 'update' => 200, 'delete' => 403],
                'cashier' => ['view' => 200, 'create' => 403, 'update' => 200, 'delete' => 403],
            ],
            'customers' => [
                'owner'   => ['view' => 200, 'create' => 201, 'update' => 200, 'delete' => 204],
                'manager' => ['view' => 200, 'create' => 201, 'update' => 200, 'delete' => 403],
                'cashier' => ['view' => 200, 'create' => 201, 'update' => 200, 'delete' => 403],
            ],
        ];

        $cases = [];
        foreach ($rules as $resource => $roles) {
            foreach ($roles as $role => $actions) {
                foreach ($actions as $action => $status) {
                    $cases["$role can $action $resource => $status"] = [$role, $resource, $action, $status];
                }
            }
        }

        return $cases;
    }

    #[DataProvider('matrix')]
    public function test_role_permissions(string $role, string $resource, string $action, int $expected): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => $role]));

        $model = $resource === 'items' ? Item::factory()->create() : Customer::factory()->create();

        $payload = $resource === 'items'
            ? ['name' => 'Mint Choc', 'category_id' => Category::factory()->create()->id, 'price' => 2.5, 'quantity' => 10]
            : ['name' => 'Jane Doe', 'phone' => '0771234567'];

        $response = match ($action) {
            'view'   => $this->getJson("/api/$resource/{$model->id}"),
            'create' => $this->postJson("/api/$resource", $payload),
            'update' => $this->patchJson("/api/$resource/{$model->id}", ['name' => 'Updated Name']),
            'delete' => $this->deleteJson("/api/$resource/{$model->id}"),
        };

        $response->assertStatus($expected);
    }

    public function test_validation_errors_return_422(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'owner']));

        $this->postJson('/api/items', ['name' => ''])->assertStatus(422);
        $this->postJson('/api/customers', [])->assertStatus(422);
    }

    public function test_missing_record_returns_404(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'owner']));

        $this->getJson('/api/items/9999')->assertStatus(404);
    }
}