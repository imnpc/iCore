<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PayStatus: int implements hasLabel
{
    // 付款状态 0-未支付 1-支付中 2-已支付
    case UNPAID = 0;
    case PAID = 1;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNPAID => '未支付',
            self::PAID => '已支付',
        };
    }
}
