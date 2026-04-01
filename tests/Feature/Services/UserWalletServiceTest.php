<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Models\WalletType;
use App\Services\UserWalletService;
use Bavix\Wallet\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserWalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_store_can_deposit_and_withdraw(): void
    {
        $service = app(UserWalletService::class);
        $user = User::factory()->create();
        $walletType = WalletType::query()->where('slug', '=', 'MONEY')->firstOrFail();

        $this->assertTrue($service->store($user->id, $walletType->id, 100.25));
        $this->assertTrue($service->store($user->id, $walletType->id, -30.10));

        $balance = $service->checkBalance($user->id, $walletType->id);

        $this->assertEqualsWithDelta(70.15, (float) $balance, 0.0001);
    }

    public function test_store_returns_false_when_amount_is_zero(): void
    {
        $service = app(UserWalletService::class);
        $user = User::factory()->create();
        $walletType = WalletType::query()->where('slug', '=', 'MONEY')->firstOrFail();

        $this->assertFalse($service->store($user->id, $walletType->id, 0));
        $this->assertEqualsWithDelta(0.0, (float) $service->checkBalance($user->id, $walletType->id), 0.0001);
    }

    public function test_check_wallet_creates_missing_enabled_wallets(): void
    {
        $service = app(UserWalletService::class);
        $user = User::factory()->create();

        $service->checkWallet($user->id);

        $user->refresh();
        $enabledWalletTypeSlugs = WalletType::query()
            ->where('is_enabled', '=', 1)
            ->pluck('slug')
            ->all();

        foreach ($enabledWalletTypeSlugs as $slug) {
            $this->assertTrue($user->hasWallet($slug));
        }
    }

    public function test_yesterday_and_total_calculate_deposit_amounts(): void
    {
        $service = app(UserWalletService::class);
        $user = User::factory()->create();
        $walletType = WalletType::query()->where('slug', '=', 'MONEY')->firstOrFail();

        Carbon::setTestNow('2026-04-01 10:00:00');
        $service->store($user->id, $walletType->id, 20);

        Carbon::setTestNow('2026-04-02 10:00:00');
        $service->store($user->id, $walletType->id, 10);
        $service->store($user->id, $walletType->id, -4);
        $wallet = $user->getWallet($walletType->slug);
        $firstDeposit = $wallet->transactions()
            ->where('type', '=', Transaction::TYPE_DEPOSIT)
            ->orderBy('id')
            ->firstOrFail();
        $firstDeposit->forceFill([
            'created_at' => Carbon::yesterday()->startOfDay(),
        ])->save();

        $yesterday = $service->yesterday($user->id, $walletType->id);
        $total = $service->total($user->id, $walletType->id);

        $this->assertEqualsWithDelta(20, (float) $yesterday, 0.0001);
        $this->assertEqualsWithDelta(30, (float) $total, 0.0001);
    }

    public function test_wallet_balance_and_wallet_total_are_aggregated_by_wallet_type(): void
    {
        $service = app(UserWalletService::class);
        $walletType = WalletType::query()->where('slug', '=', 'MONEY')->firstOrFail();
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $service->store($userA->id, $walletType->id, 10);
        $service->store($userA->id, $walletType->id, -2);
        $service->store($userB->id, $walletType->id, 20);

        $userABalance = (float) $service->checkBalance($userA->id, $walletType->id);
        $userBBalance = (float) $service->checkBalance($userB->id, $walletType->id);
        $userATotal = (float) $service->total($userA->id, $walletType->id);
        $userBTotal = (float) $service->total($userB->id, $walletType->id);
        $walletBalance = $service->walletBalance($walletType->id);
        $walletTotal = $service->walletTotal($walletType->id);

        $this->assertEqualsWithDelta($userABalance + $userBBalance, (float) $walletBalance, 0.0001);
        $this->assertEqualsWithDelta($userATotal + $userBTotal, (float) $walletTotal, 0.0001);
    }
}
