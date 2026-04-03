<?php

namespace App\Filament\Actions;

use App\Enums\FromType;
use App\Models\WalletType;
use App\Services\LogService;
use App\Services\UserWalletService;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

/**
 * 钱包余额调整动作。
 */
class WalletAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'recharge';
    }

    protected function setUp(): void
    {
        $this->icon('heroicon-s-currency-yen');
        $this->tooltip(__('filament-model.attributes.user.tooltip'));
        $this->label(__('filament-model.attributes.user.recharge'));
        $this->schema(function ($record) {
            $userWalletService = app(UserWalletService::class);
            $wallets = $userWalletService->getUserWallets($record->id); // 获取用户账户各种积分余额
            $list = WalletType::query()
                ->where('is_enabled', '=', 1)
                ->get(['id', 'name', 'slug']);
            $options = [];
            foreach ($list as $key => $value) {
                $name = strtolower($value->slug);
                $balance = $wallets[$name.'_balance'] ?? 0;
                $options[$value->id] = $value->name.' [ 当前: '.$balance.' ]';
            }

            return [
                // 钱包类型
                Radio::make('wallet_type')
                    ->default((string) ($list->first()?->id ?? ''))
                    ->options($options)
                    ->label(__('filament-model.attributes.user.wallet_type'))
                    ->helperText('请选择要充值的钱包类型')
                    ->required()
                    ->live()
                    ->inline()
                    ->inlineLabel(false),
                // 操作类型
                Radio::make('type')
                    ->default('credit')
                    ->maxWidth('xs')
                    ->options([
                        'credit' => trans('filament-wallet::messages.wallets.action.credit'),
                        'debit' => trans('filament-wallet::messages.wallets.action.debit'),
                    ])
                    ->label(trans('filament-wallet::messages.wallets.action.type'))
                    ->required()
                    ->live()
                    ->inline()
                    ->inlineLabel(false),
                // 数量
                TextInput::make('money')
                    ->label(__('filament-model.attributes.user.money'))
                    ->helperText('请输入数量,最低值为 1')
                    ->numeric()
                    ->minValue(1)
                    ->maxWidth('xs')
                    ->required()
                    ->live(),
                // 备注
                TextInput::make('remark')
                    ->label(__('filament-model.attributes.user.remark'))
                    ->helperText('请输入备注'),
            ];
        });
        $this->action(function ($record, array $data) {

            $logService = app(LogService::class); // 钱包服务初始化
            $userWalletService = app(UserWalletService::class);
            $walletType = WalletType::query()->find($data['wallet_type']);
            if (! $walletType) {
                Notification::make()
                    ->title('操作失败')
                    ->body('钱包类型不存在')
                    ->danger()
                    ->send();

                return;
            }

            $walletName = $walletType->name;

            if ($data['remark']) {
                $remark = $data['remark'];
            } else {
                $remark = $data['type'] === 'debit'
                    ? '后台管理员扣除 '.$walletName.' ,数量: '.$data['money']
                    : '后台管理员充值 '.$walletName.' ,数量: '.$data['money'];
            }
            $money = $data['money']; // 操作数量
            // 需要处理扣除不能超过账户余额数量
            if ($data['type'] === 'debit') {
                $balance = $userWalletService->checkBalance($record->id, $data['wallet_type']);
                if (abs($data['money']) > $balance) {
                    Notification::make()
                        ->title('操作失败')
                        ->body('扣除数量不能超过账户余额数量')
                        ->danger()
                        ->send();

                    return;
                }
                $money = -$data['money']; // 扣除金额
            }

            $executed = $logService->userWalletLog($record->id, $data['wallet_type'], $money, 0, '', FromType::ADMIN->value, $remark);
            if (! $executed) {
                Notification::make()
                    ->title('操作失败')
                    ->body('钱包操作执行失败，请重试')
                    ->danger()
                    ->send();

                return;
            }

            if ($data['type'] === 'credit') {
                Notification::make()
                    ->title('操作成功')
                    ->body('已经成功充值了 '.$walletName.' ,数量: '.$data['money'])
                    ->success()
                    ->send();
            } elseif ($data['type'] === 'debit') {
                Notification::make()
                    ->title('操作成功')
                    ->body('已经成功扣除了 '.$walletName.' ,数量: '.$data['money'])
                    ->warning()
                    ->send();
            }
        });
    }
}
