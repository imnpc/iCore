<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * 账变来源类型枚举。
 */
enum FromType: int implements HasLabel
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
            self::ADMIN => __('filament-model.enums.labels.from_type.admin'),
            self::DEFAULT => __('filament-model.enums.labels.from_type.default'),
            self::ORDER => __('filament-model.enums.labels.from_type.order'),
            self::RECHARGE => __('filament-model.enums.labels.from_type.recharge'),
            self::WITHDRAW => __('filament-model.enums.labels.from_type.withdraw'),
            self::INVITE => __('filament-model.enums.labels.from_type.invite'),
            self::REGISTER => __('filament-model.enums.labels.from_type.register'),
            self::SIGN => __('filament-model.enums.labels.from_type.sign'),
            self::OTHER => __('filament-model.enums.labels.from_type.other'),
        };
    }
}
