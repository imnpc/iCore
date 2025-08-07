<?php

namespace App\Filament\Resources\WalletTypes\Pages;

use App\Filament\Resources\WalletTypes\WalletTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWalletTypes extends ListRecords
{
    protected static string $resource = WalletTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
