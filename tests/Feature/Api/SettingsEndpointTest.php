<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SettingsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_read_settings(): void
    {
        $this->getJson('/api/v1/settings/allSettings?key=app')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_read_only_public_settings(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/settings/allSettings?key=app')
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->getJson('/api/v1/settings/allSettings?key=pay')
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['key']]);
    }

    public function test_password_reset_does_not_reveal_unknown_email_addresses(): void
    {
        Cache::put('verificationCode_unknown@example.com', [
            'email' => 'unknown@example.com',
            'code' => '123456',
        ], now()->addMinutes(5));

        $this->postJson('/api/v1/user/emailForgetPassword', [
            'email' => 'unknown@example.com',
            'email_verify_code' => '123456',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertOk();
    }
}
