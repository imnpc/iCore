<?php

namespace App\Filament\Actions;

use App\Enums\FromType;
use App\Models\WalletType;
use App\Services\LogService;
use App\Services\UserWalletService;
use Carbon\Carbon;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

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
            $UserWalletService = app()->make(UserWalletService::class);
            $wallets = $UserWalletService->getUserWallets($record->id); // 获取用户账户各种积分余额
            $list = WalletType::all();
            $options = [];
            foreach ($list as $key => $value) {
                $name = strtolower($value->slug);
                $balance = $wallets[$name . '_balance'];
                $options[$value->id] = $value->name . ' [ 当前: ' . $balance . ' ]';
            }
            return [
                // 钱包类型
                Radio::make('wallet_type')
                    ->default('1')
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
                        'debit' => trans('filament-wallet::messages.wallets.action.debit')
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

            $logService = app()->make(LogService::class); // 钱包服务初始化
            $UserWalletService = app()->make(UserWalletService::class);
            $day = Carbon::now()->toDateString();
            $wallet_name = WalletType::find($data['wallet_type'])->name;

            if ($data['remark']) {
                $remark = $data['remark'];
            } else {
                if ($data['type'] == 'credit') {
                    $remark = "后台管理员充值 ".$wallet_name." ,数量: " . $data['money'];
                } elseif ($data['type'] == 'debit') {
                    $remark = "后台管理员扣除 ".$wallet_name." ,数量: " . $data['money'];
                }
            }
            $money = $data['money']; // 操作数量
            // 需要处理扣除不能超过账户余额数量
            if ($data['type'] == 'debit') {
                $balance = $UserWalletService->checkBalance($record->id, $data['wallet_type']);
                if (abs($data['money']) > $balance) {
                    Notification::make()
                        ->title('操作失败')
                        ->body("扣除数量不能超过账户余额数量")
                        ->danger()
                        ->send();
                    return;
                }
                $money = -$data['money']; // 扣除金额
            }

            $logService->userWalletLog($record->id, $data['wallet_type'], $money, 0, $day, FromType::ADMIN->value, $remark);

            if ($data['type'] == 'credit') {
                Notification::make()
                    ->title('操作成功')
                    ->body("已经成功充值了 ".$wallet_name." ,数量: " . $data['money'])
                    ->success()
                    ->send();
            } elseif ($data['type'] == 'debit') {
                Notification::make()
                    ->title('操作成功')
                    ->body("已经成功扣除了 ".$wallet_name." ,数量: " . $data['money'])
                    ->warning()
                    ->send();
            }
        });
    }
}
