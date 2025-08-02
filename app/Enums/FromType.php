<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FromType: int implements hasLabel
{
    // 积分来源
    // 核心操作项
    case ADMIN = -1; // 后台
    case DEFAULT = 0; // 默认
    case ORDER = 1; // 订单
    case RECHARGE = 2; // 充值
    case WITHDRAW = 3; // 提现
    // 其他杂项
    case INVITE = 51; // 邀请
    case REGISTER = 52; // 注册
    case SIGN = 53; // 签到
    case OTHER = 99;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMIN => '后台',
            self::ORDER => '订单',
            self::RECHARGE => '充值',
            self::WITHDRAW => '提现',
            self::INVITE => '邀请',
            self::REGISTER => '注册',
            self::SIGN => '签到',
            self::OTHER => '其他',
            default => '默认',
        };
    }
}
