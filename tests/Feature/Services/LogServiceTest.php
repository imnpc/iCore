<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Models\UserWalletLog;
use App\Models\WalletType;
use App\Services\LogService;
use App\Services\UserWalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_wallet_log_records_wallet_change_and_log_row(): void
    {
        $logService = app(LogService::class);
        $walletService = app(UserWalletService::class);
        $user = User::factory()->create();
        $walletType = WalletType::query()->where('slug', '=', 'MONEY')->firstOrFail();

        $result = $logService->userWalletLog(
            uid: $user->id,
            wallet_type_id: $walletType->id,
            add: 12.50,
            from_uid: 0,
            day: '2026-04-02',
            from: 1,
            remark: 'test income',
            order_id: 1001,
        );
        $this->assertTrue($result);

        $this->assertEqualsWithDelta(
            12.5,
            (float) $walletService->checkBalance($user->id, $walletType->id),
            0.0001,
        );
        $this->assertDatabaseHas('user_wallet_logs', [
            'user_id' => $user->id,
            'wallet_type_id' => $walletType->id,
            'add' => 12.50,
            'remark' => 'test income',
            'order_id' => 1001,
        ]);
    }

    public function test_user_wallet_log_does_not_create_log_when_wallet_store_fails(): void
    {
        $logService = app(LogService::class);
        $walletService = app(UserWalletService::class);
        $user = User::factory()->create();
        $walletType = WalletType::query()->where('slug', '=', 'MONEY')->firstOrFail();

        $result = $logService->userWalletLog(
            uid: $user->id,
            wallet_type_id: $walletType->id,
            add: -5,
            from_uid: 0,
            day: '2026-04-02',
            from: 1,
            remark: 'test withdraw',
            order_id: 1002,
        );
        $this->assertFalse($result);

        $this->assertEqualsWithDelta(
            0.0,
            (float) $walletService->checkBalance($user->id, $walletType->id),
            0.0001,
        );
        $this->assertSame(0, UserWalletLog::query()->count());
    }

    public function test_user_wallet_log_ignores_zero_amount(): void
    {
        $logService = app(LogService::class);
        $walletService = app(UserWalletService::class);
        $user = User::factory()->create();
        $walletType = WalletType::query()->where('slug', '=', 'MONEY')->firstOrFail();

        $result = $logService->userWalletLog($user->id, $walletType->id, 0);
        $this->assertFalse($result);

        $this->assertEqualsWithDelta(
            0.0,
            (float) $walletService->checkBalance($user->id, $walletType->id),
            0.0001,
        );
        $this->assertSame(0, UserWalletLog::query()->count());
    }
}
