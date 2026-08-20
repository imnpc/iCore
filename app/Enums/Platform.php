<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * 来源平台枚举。
 */
enum Platform: int implements HasLabel
{
    // 1-PC 2-H5 3-公众号 4-安卓APP 5-苹果APP 6-小程序
    case PC = 1;
    case H5 = 2;
    case MP = 3;
    case ANDROIDAPP = 4;
    case IOSAPP = 5;
    case MINIPROGRAM = 6;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PC => __('filament-model.enums.labels.platform.pc'),
            self::H5 => __('filament-model.enums.labels.platform.h5'),
            self::MP => __('filament-model.enums.labels.platform.mp'),
            self::ANDROIDAPP => __('filament-model.enums.labels.platform.androidapp'),
            self::IOSAPP => __('filament-model.enums.labels.platform.iosapp'),
            self::MINIPROGRAM => __('filament-model.enums.labels.platform.miniprogram'),
        };
    }
}
