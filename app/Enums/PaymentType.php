<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * 支付类型枚举。
 */
enum PaymentType: int implements HasLabel
{
    // 支付网关 1-支付宝 2-微信
    case ALIPAY = 1;
    case WECHAT = 2;
    case BALANCE = 88;
    case ADMIN = 99;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALIPAY => __('filament-model.enums.labels.payment_type.alipay'),
            self::WECHAT => __('filament-model.enums.labels.payment_type.wechat'),
            self::BALANCE => __('filament-model.enums.labels.payment_type.balance'),
            self::ADMIN => __('filament-model.enums.labels.payment_type.admin'),
        };
    }
}
