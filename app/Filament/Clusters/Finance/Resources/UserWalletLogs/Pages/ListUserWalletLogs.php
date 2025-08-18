<?php

namespace App\Filament\Clusters\Finance\Resources\UserWalletLogs\Pages;

use App\Filament\Clusters\Finance\Resources\UserWalletLogResource;
use Filament\Resources\Pages\ListRecords;

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
