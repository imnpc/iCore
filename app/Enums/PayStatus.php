<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PayStatus: int implements hasLabel
{
    // 支付状态 0-未支付 1-待审核 2-已支付
    case UNPAID = 0;
    case PAYING = 1;
    case PAID = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNPAID => '未支付',
            self::PAYING => '待审核',
            self::PAID => '已支付',
        };
    }
}
