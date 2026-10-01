<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BannedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_banned_user_cannot_access_api(): void
    {
        $user = User::factory()->create();
        $user->setBannedFlag()->save();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/user/me')
            ->assertForbidden();
    }

    public function test_banned_user_cannot_login(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $user->setBannedFlag()->save();

        $this->mock('captcha', function ($mock): void {
            $mock->shouldReceive('check_api')->once()->andReturnTrue();
        });

        $this->postJson('/api/v1/user/login', [
            'email' => $user->email,
            'password' => 'password',
            'captcha_key' => 'captcha-key',
            'captcha' => 'captcha',
        ])->assertForbidden();

        $this->assertFalse(Auth::guard('web')->check());
    }
}
