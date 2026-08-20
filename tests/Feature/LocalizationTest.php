<?php

namespace Tests\Feature;

use App\Enums\FromType;
use App\Enums\PaymentType;
use App\Enums\PayStatus;
use App\Enums\PayType;
use App\Enums\Platform;
use App\Enums\WithdrawStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\Middleware\StartSession;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_uses_accept_language_and_emits_language_headers(): void
    {
        $response = $this->getJson('/api/v1/user/me', [
            'Accept-Language' => 'en-US,en;q=0.9',
        ]);

        $response
            ->assertUnauthorized()
            ->assertHeader('Content-Language', 'en')
            ->assertHeader('Vary', 'Accept-Language')
            ->assertJsonPath('message', 'Authentication failed. Please sign in again');
    }

    public function test_api_validation_messages_follow_the_resolved_locale(): void
    {
        $this->postJson('/api/v1/user/login', [], [
            'Accept-Language' => 'en-US,en;q=0.9',
        ])
            ->assertUnprocessable()
            ->assertHeader('Content-Language', 'en')
            ->assertJsonPath('errors.email.0', 'The email field is required.');

        $this->postJson('/api/v1/user/login?locale=zh_CN')
            ->assertUnprocessable()
            ->assertHeader('Content-Language', 'zh_CN')
            ->assertJsonPath('errors.email.0', 'email 不能为空。');
    }

    public function test_api_locale_query_parameter_takes_precedence_and_unsupported_locale_falls_back_to_chinese(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/user/me?locale=en', [
            'Accept-Language' => 'zh-CN',
        ])->assertOk()->assertHeader('Content-Language', 'en');

        $this->getJson('/api/v1/user/me?locale=zh_TW', [
            'Accept-Language' => 'zh-TW',
        ])->assertOk()->assertHeader('Content-Language', 'zh_CN');
    }

    public function test_api_uses_session_locale_preference(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->withSession(['locale' => 'en'])
            ->getJson('/api/v1/user/me', ['Accept-Language' => 'zh-CN'])
            ->assertOk()
            ->assertHeader('Content-Language', 'en');

    }

    public function test_api_uses_cookie_locale_preference(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->withoutMiddleware(StartSession::class)
            ->withCredentials()
            ->withUnencryptedCookie('locale', 'en')
            ->getJson('/api/v1/user/me', ['Accept-Language' => 'zh-CN'])
            ->assertOk()
            ->assertHeader('Content-Language', 'en');
    }

    public function test_all_enum_labels_follow_the_current_locale(): void
    {
        app()->setLocale('zh_CN');
        $this->assertSame('后台', FromType::ADMIN->getLabel());
        $this->assertSame('未支付', PayStatus::UNPAID->getLabel());
        $this->assertSame('订单', PayType::ORDER->getLabel());
        $this->assertSame('支付宝', PaymentType::ALIPAY->getLabel());
        $this->assertSame('公众号', Platform::MP->getLabel());
        $this->assertSame('待审核', WithdrawStatus::PENDING->getLabel());

        app()->setLocale('en');
        $this->assertSame('Admin', FromType::ADMIN->getLabel());
        $this->assertSame('Unpaid', PayStatus::UNPAID->getLabel());
        $this->assertSame('Order', PayType::ORDER->getLabel());
        $this->assertSame('Alipay', PaymentType::ALIPAY->getLabel());
        $this->assertSame('Official account', Platform::MP->getLabel());
        $this->assertSame('Pending review', WithdrawStatus::PENDING->getLabel());
    }

    public function test_filament_model_translation_keys_are_available_in_both_supported_locales(): void
    {
        $translations = require lang_path('zh_CN/filament-model.php');

        foreach ($this->flattenTranslationKeys($translations) as $key) {
            $translationKey = "filament-model.{$key}";

            $this->assertNotSame($translationKey, trans($translationKey, [], 'zh_CN'));
            $this->assertNotSame($translationKey, trans($translationKey, [], 'en'));
        }
    }

    /**
     * @param  array<string, mixed>  $translations
     * @return array<int, string>
     */
    private function flattenTranslationKeys(array $translations, string $prefix = ''): array
    {
        $keys = [];

        foreach ($translations as $key => $value) {
            $path = $prefix === '' ? $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $keys = [...$keys, ...$this->flattenTranslationKeys($value, $path)];

                continue;
            }

            $keys[] = $path;
        }

        return $keys;
    }
}
