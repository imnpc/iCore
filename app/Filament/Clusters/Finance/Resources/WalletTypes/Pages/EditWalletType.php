<?php

namespace App\Filament\Clusters\Finance\Resources\WalletTypes\Pages;

use App\Filament\Clusters\Finance\Resources\WalletTypeResource;
use Filament\Resources\Pages\EditRecord;

/**
 * 钱包类型编辑页面。
 */
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
