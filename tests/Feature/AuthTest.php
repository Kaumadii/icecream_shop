<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_token(): void
    {
        User::factory()->create(['email' => 'a@test.com', 'password' => 'secret123', 'role' => 'owner']);

        $this->postJson('/api/auth/login', ['email' => 'a@test.com', 'password' => 'secret123'])
            ->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'name', 'email', 'role']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'a@test.com', 'password' => 'secret123']);

        $this->postJson('/api/auth/login', ['email' => 'a@test.com', 'password' => 'wrong'])
            ->assertStatus(401);
    }

    public function test_login_validates_input(): void
    {
        $this->postJson('/api/auth/login', [])->assertStatus(422);
    }

    public function test_protected_routes_require_authentication(): void
    {
        $this->getJson('/api/items')->assertStatus(401);
        $this->getJson('/api/customers')->assertStatus(401);
    }

    public function test_logout_revokes_token(): void
    {
        User::factory()->create(['email' => 'a@test.com', 'password' => 'secret123']);

        $token = $this->postJson('/api/auth/login', ['email' => 'a@test.com', 'password' => 'secret123'])
            ->json('token');

        $this->withToken($token)->postJson('/api/auth/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
