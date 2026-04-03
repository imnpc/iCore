<?php

namespace App\Filament\Clusters\Finance\Resources\Wallets\Pages;

use App\Filament\Clusters\Finance\Resources\WalletResource;
use Filament\Resources\Pages\ListRecords;

/**
 * 钱包列表页面。
 */
class ListWallets extends ListRecords
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
