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
                $options[$value->id] = $value->name.' ['.trans('filament-model.ui.labels.current_balance', ['balance' => $balance]).']';
            }

            return [
                // 钱包类型
                Radio::make('wallet_type')
                    ->default((string) ($list->first()?->id ?? ''))
                    ->options($options)
                    ->label(__('filament-model.attributes.user.wallet_type'))
                    ->helperText(__('filament-model.ui.labels.choose_wallet_type'))
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
                    ->helperText(__('filament-model.ui.labels.enter_amount_minimum'))
                    ->numeric()
                    ->minValue(1)
                    ->maxWidth('xs')
                    ->required()
                    ->live(),
                // 备注
                TextInput::make('remark')
                    ->label(__('filament-model.attributes.user.remark'))
                    ->helperText(__('filament-model.ui.labels.enter_remark')),
            ];
        });
        $this->action(function ($record, array $data) {

            $logService = app(LogService::class); // 钱包服务初始化
            $userWalletService = app(UserWalletService::class);
            $walletType = WalletType::query()->find($data['wallet_type']);
            if (! $walletType) {
                Notification::make()
                    ->title(__('filament-model.ui.labels.operation_failed'))
                    ->body(__('filament-model.ui.labels.wallet_type_not_found'))
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
                        ->title(__('filament-model.ui.labels.operation_failed'))
                        ->body(__('filament-model.ui.labels.debit_exceeds_balance'))
                        ->danger()
                        ->send();

                    return;
                }
                $money = -$data['money']; // 扣除金额
            }

            $executed = $logService->userWalletLog($record->id, $data['wallet_type'], $money, 0, '', FromType::ADMIN->value, $remark);
            if (! $executed) {
                Notification::make()
                    ->title(__('filament-model.ui.labels.operation_failed'))
                    ->body(__('filament-model.ui.labels.wallet_operation_failed'))
                    ->danger()
                    ->send();

                return;
            }

            if ($data['type'] === 'credit') {
                Notification::make()
                    ->title(__('filament-model.ui.labels.operation_succeeded'))
                    ->body(__('filament-model.ui.labels.recharge_completed', ['wallet' => $walletName, 'amount' => $data['money']]))
                    ->success()
                    ->send();
            } elseif ($data['type'] === 'debit') {
                Notification::make()
                    ->title(__('filament-model.ui.labels.operation_succeeded'))
                    ->body(__('filament-model.ui.labels.debit_completed', ['wallet' => $walletName, 'amount' => $data['money']]))
                    ->warning()
                    ->send();
            }
        });
    }
}
