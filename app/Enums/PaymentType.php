<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentType: int implements hasLabel
{
    // 支付网关 1-支付宝 2-微信
    case ALIPAY = 1;
    case WECHAT = 2;
    case BALANCE = 88;
    case ADMIN = 99;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALIPAY => '支付宝',
            self::WECHAT => '微信',
            self::BALANCE => '余额',
            self::ADMIN => '管理员',
        };
    }
}
