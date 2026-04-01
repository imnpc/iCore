<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletType;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Bavix\Wallet\Internal\Service\MathServiceInterface;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Wallet;
use Bavix\Wallet\Services\CastServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserWalletService
{
    public function __construct(
        private readonly MathServiceInterface $mathService,
        private readonly CastServiceInterface $castService,
    ) {}

    /**
     * 写入钱包数据
     *
     * @param  int  $uid  用户 ID
     * @param  int  $wallet_type_id  钱包类型 ID
     * @param  float|int  $money  操作金额
     * @param  array|null  $remark  备注
     *
     * @throws ExceptionInterface
     * @throws Throwable
     */
    public function store(int $uid, int $wallet_type_id, float|int $money, ?array $remark = null): bool
    {
        if ((float) $money === 0.0) {
            return false;
        }

        try {
            $wallet = $this->resolveWalletByUserAndType($uid, $wallet_type_id); // 获取用户指定钱包

            return DB::transaction(function () use ($wallet, $money, $remark): bool {
                $wallet->balanceInt; // 钱包进入事务锁定模式

                // 动态选择方法名称
                $operation = $money > 0 ? 'deposit' : 'withdraw';
                $methodSuffix = $wallet->decimal_places > 0 ? 'Float' : '';
                $methodName = $operation.$methodSuffix;

                // 执行钱包操作
                $wallet->{$methodName}(abs($money), $remark);

                return true;
            });
        } catch (Throwable $e) {
            // 异常处理
            Log::error(__METHOD__.'|'.__METHOD__.'-UserWalletService-store-执行失败', ['error' => $e]);

            return false;
        }
    }

    /**
     * 检测用户钱包,没有的钱包类型自动创建
     *
     * @param  int  $uid  用户 ID
     */
    public function checkWallet(int $uid): void
    {
        $user = User::query()->with('wallets')->findOrFail($uid); // 获取用户信息
        $walletTypes = WalletType::query()
            ->where('is_enabled', '=', 1)
            ->get(['id', 'name', 'slug', 'description', 'decimal_places']); // 钱包类型列表: 状态为 启用 的
        $walletSlugs = $user->wallets
            ->pluck('slug')
            ->map(static fn (string $slug): string => strtolower($slug))
            ->all();

        foreach ($walletTypes as $value) {
            if (! in_array(strtolower($value->slug), $walletSlugs, true)) {
                $user->createWallet([
                    'name' => $value->name, // 钱包名称
                    'slug' => $value->slug, // 钱包代码
                    'description' => '用户 '.$user->id.' 的 '.$value->description, // 钱包介绍
                    'decimal_places' => $value->decimal_places, // 钱包小数位数
                ]); // 创建钱包
            }
        }
    }

    /**
     * 获得用户指定钱包余额
     *
     * @param  int  $uid  用户 ID
     * @param  int  $wallet_type_id  钱包类型 ID
     *
     * @throws Throwable
     */
    public function checkBalance(int $uid, int $wallet_type_id): int|float|string
    {
        $wallet = $this->resolveWalletByUserAndType($uid, $wallet_type_id); // 获取用户指定钱包

        return DB::transaction(function () use ($wallet): int|float|string {
            $wallet->refreshBalance(); // 强制刷新用户该钱包余额
            $wallet->balanceInt; // 钱包进入事务锁定模式

            // 根据小数位数动态选择余额字段
            return $wallet->decimal_places > 0 ? $wallet->balanceFloat : $wallet->balance;
        });
    }

    /**
     * 用户指定钱包昨日增加金额
     *
     * @param  int  $uid  用户 ID
     * @param  int  $wallet_type_id  钱包类型 ID
     */
    public function yesterday(int $uid, int $wallet_type_id): int|string
    {
        $wallet = $this->resolveWalletByUserAndType($uid, $wallet_type_id); // 获取用户指定钱包
        $amount = $wallet->transactions()
            ->where('type', '=', Transaction::TYPE_DEPOSIT)
            ->whereDate('created_at', '=', Carbon::yesterday())
            ->sum('amount'); // 昨日增加金额

        return $this->formatAmountByWallet($wallet, $amount);
    }

    /**
     * 用户指定钱包累计收入金额
     *
     * @param  int  $uid  用户 ID
     * @param  int  $wallet_type_id  钱包类型 ID
     */
    public function total(int $uid, int $wallet_type_id): int|string
    {
        $wallet = $this->resolveWalletByUserAndType($uid, $wallet_type_id); // 获取用户指定钱包
        $amount = $wallet->transactions()
            ->where('type', '=', Transaction::TYPE_DEPOSIT)
            ->sum('amount'); // 累计收入

        return $this->formatAmountByWallet($wallet, $amount);
    }

    /**
     * 指定类型钱包当前总余额
     *
     * @param  int  $wallet_type_id  钱包类型 ID
     */
    public function walletBalance(int $wallet_type_id): int|string
    {
        $walletType = WalletType::query()->findOrFail($wallet_type_id); // 获取钱包类型信息
        $walletQuery = Wallet::query()
            ->whereRaw('LOWER(slug) = ?', [strtolower($walletType->slug)]);
        $wallet = (clone $walletQuery)->first(); // 指定钱包信息
        if (! $wallet) {
            return 0;
        }

        $walletIds = $walletQuery->pluck('id')->all();
        $amount = Transaction::query()
            ->whereIn('wallet_id', $walletIds)
            ->sum('amount'); // 指定类型钱包当前总余额

        return $this->formatAmountByWallet($wallet, $amount);
    }

    /**
     * 指定类型钱包累计收入
     *
     * @param  int  $wallet_type_id  钱包类型 ID
     */
    public function walletTotal(int $wallet_type_id): int|string
    {
        $walletType = WalletType::query()->findOrFail($wallet_type_id); // 获取钱包类型信息
        $wallet = Wallet::query()
            ->whereRaw('LOWER(slug) = ?', [strtolower($walletType->slug)])
            ->first(); // 指定钱包信息
        if (! $wallet) {
            return 0;
        }

        $walletIds = Wallet::query()
            ->whereRaw('LOWER(slug) = ?', [strtolower($walletType->slug)])
            ->pluck('id')
            ->all();

        $amount = Transaction::query()
            ->where('type', '=', Transaction::TYPE_DEPOSIT)
            ->whereIn('wallet_id', $walletIds)
            ->sum('amount'); // 指定类型钱包累计收入

        return $this->formatAmountByWallet($wallet, $amount);
    }

    /**
     * 获取某个用户的所有钱包余额
     *
     * @param  int  $uid  用户 ID
     */
    public function getUserWallets(int $uid): array
    {
        $this->checkWallet($uid); // 检测用户钱包
        $data = [];
        $user = User::query()->with('wallets')->findOrFail($uid);
        $walletTypeSlugs = $user->wallets
            ->pluck('slug')
            ->map(static fn (string $slug): string => strtolower($slug))
            ->all();
        $walletTypes = WalletType::query()
            ->get(['id', 'name', 'slug'])
            ->filter(static fn (WalletType $walletType): bool => in_array(strtolower($walletType->slug), $walletTypeSlugs, true))
            ->keyBy(static fn (WalletType $walletType): string => strtolower($walletType->slug));

        foreach ($user->wallets as $v) {
            $type = $walletTypes->get(strtolower($v->slug));
            if (! $type) {
                continue;
            }

            $name = strtolower($v->slug);
            $data[$name.'_id'] = $type->id;
            $data[$name.'_name'] = $type->name;
            $data[$name.'_balance'] = $v->balanceFloat;
        }

        return $data;
    }

    private function resolveWalletByUserAndType(int $uid, int $walletTypeId): Wallet
    {
        $user = User::query()->findOrFail($uid); // 获取用户信息
        $this->checkWallet($uid); // 检测用户钱包
        $walletType = WalletType::query()->findOrFail($walletTypeId); // 获取钱包类型信息

        return $user->getWallet($walletType->slug); // 获取用户指定钱包
    }

    private function formatAmountByWallet(Wallet $wallet, int|string $amount): int|string
    {
        if ((string) $amount === '0' || (string) $amount === '0.0' || (string) $amount === '0.00') {
            return 0;
        }

        $decimalPlacesValue = $this->castService
            ->getWallet($wallet)
            ->decimal_places;
        $decimalPlaces = $this->mathService->powTen($decimalPlacesValue);

        return $this->mathService->div($amount, $decimalPlaces, $decimalPlacesValue); // 格式化输出
    }
}
