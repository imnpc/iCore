<?php

namespace App\Filament\Resources\WalletTypes\Pages;

use App\Filament\Resources\WalletTypes\WalletTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
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
