<?php

namespace App\Filament\Resources\UserWalletLogs\Pages;

use App\Filament\Resources\UserWalletLogs\UserWalletLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserWalletLogs extends ListRecords
{
    protected static string $resource = UserWalletLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
