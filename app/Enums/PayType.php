<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * 支付方式枚举。
 */
enum PayType: int implements HasLabel
{
    // 支付类型 1:订单 2:充值
    case ORDER = 1;
    case RECHARGE = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ORDER => '订单',
            self::RECHARGE => '充值',
            default => null,
        };
    }
}
