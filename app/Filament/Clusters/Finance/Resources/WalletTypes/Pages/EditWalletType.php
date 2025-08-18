<?php

namespace App\Filament\Clusters\Finance\Resources\WalletTypes\Pages;

use App\Filament\Clusters\Finance\Resources\WalletTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditWalletType extends EditRecord
{
    protected static string $resource = WalletTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            DeleteAction::make(),
//            ForceDeleteAction::make(),
//            RestoreAction::make(),
        ];
    }
}
