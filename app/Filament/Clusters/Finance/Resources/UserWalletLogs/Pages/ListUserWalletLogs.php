<?php

namespace App\Filament\Clusters\Finance\Resources\UserWalletLogs\Pages;

use App\Filament\Clusters\Finance\Resources\UserWalletLogResource;
use Filament\Resources\Pages\ListRecords;

/**
 * 用户钱包日志列表页面。
 */
class ListUserWalletLogs extends ListRecords
{
    protected static string $resource = UserWalletLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            CreateAction::make(),
        ];
    }
}
