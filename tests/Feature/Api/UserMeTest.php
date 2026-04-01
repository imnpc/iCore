<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/user/me');

        $response->assertUnauthorized();
    }

    public function test_me_endpoint_returns_authenticated_user_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user/me');

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }
}
