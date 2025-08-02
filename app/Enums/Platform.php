<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Platform: int implements hasLabel
{
    // 1-PC 2-H5 3-公众号 4-安卓APP 5-苹果APP 6-小程序
    case PC = 1;
    case H5 = 2;
    case MP = 3;
    case ANDROIDAPP = 4;
    case IOSAPP = 5;
    case MINIPROGRAM = 6;

    public function getlabel(): ?string
    {
        return match ($this) {
            self::PC => 'PC',
            self::H5 => 'H5',
            self::MP => '公众号',
            self::ANDROIDAPP => '安卓APP',
            self::IOSAPP => '苹果APP',
            self::MINIPROGRAM => '小程序',
        };
    }
}
