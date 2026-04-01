<?php

namespace App\Services;

use App\Models\UserWalletLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class LogService
{
    public function __construct(
        private readonly UserWalletService $userWalletService,
    ) {}

    /**
     * 记录用户钱包日志
     *
     * @param  int  $uid  用户 ID
     * @param  int  $wallet_type_id  钱包类型 ID
     * @param  float|int  $add  更改金额 支持加减+ -
     * @param  int  $from_uid  来自用户 ID
     * @param  string  $day  所属日期
     * @param  int  $from  来源
     * @param  string  $remark  备注
     * @param  int  $order_id  订单 ID
     */
    public function userWalletLog(int $uid, int $wallet_type_id, float|int $add, int $from_uid = 0, string $day = '', int $from = 0, string $remark = '', int $order_id = 0): bool
    {
        if ((float) $add === 0.0) {
            return false;
        }

        $key = 'user_wallet_lock_'.$wallet_type_id.'_'.$uid; // 钱包缓存 key
        $lock = Cache::lock($key, 10);
        if (! $lock->get()) {
            return false;
        }

        try {
            $oldBalance = (string) $this->userWalletService->checkBalance($uid, $wallet_type_id); // 获取刷新用户钱包余额
            $amount = (string) $add;
            $scale = $this->resolveScale($oldBalance, $amount);
            $newBalance = $add >= 0
                ? bcadd($oldBalance, $amount, $scale) // 增加
                : bcsub($oldBalance, ltrim($amount, '-'), $scale); // 减少
            $logDay = $day ?: Carbon::now()->toDateString();

            DB::transaction(function () use ($uid, $wallet_type_id, $add, $from_uid, $logDay, $from, $remark, $order_id, $oldBalance, $newBalance): void {
                // 写入数据到钱包和日志
                if (! $this->userWalletService->store($uid, $wallet_type_id, $add)) {
                    throw new RuntimeException('Wallet operation failed.');
                }

                UserWalletLog::query()->create([
                    'user_id' => $uid, // 用户 ID
                    'wallet_type_id' => $wallet_type_id, // 钱包类型 ID
                    'from_user_id' => $from_uid, // 来自用户 ID
                    'day' => $logDay, // 日期
                    'old' => $oldBalance, // 原数值
                    'add' => $add, // 新增
                    'new' => $newBalance, // 新数值
                    'from' => $from, // 来源
                    'remark' => $remark, // 备注
                    'order_id' => $order_id, // 订单 ID
                ]); // 记录钱包日志
            });

            return true;
        } catch (Throwable $e) {
            // 异常处理
            Log::error(__METHOD__.'|'.__METHOD__.'-执行失败', ['error' => $e]);

            return false;
        } finally {
            $lock->release();
        }
    }

    private function resolveScale(string ...$amounts): int
    {
        $scales = array_map(function (string $amount): int {
            $parts = explode('.', $amount);

            return isset($parts[1]) ? strlen(rtrim($parts[1], '0')) : 0;
        }, $amounts);

        return max($scales);
    }
}
