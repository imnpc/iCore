<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * 提现状态枚举。
 */
enum WithdrawStatus: int implements HasLabel
{
    // 审核状态:0-待审核 1-已转账 -1-驳回
    case PENDING = 0;
    case SUCCESS = 1;
    case FAILED = -1;

    public function getlabel(): ?string
    {
        return match ($this) {
            self::PENDING => '待审核',
            self::SUCCESS => '已转账',
            self::FAILED => '驳回',
        };
    }
}
