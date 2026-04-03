<?php

namespace App\Filament\Clusters\Finance\Resources\WalletTypes\Pages;

use App\Filament\Clusters\Finance\Resources\WalletTypeResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * 钱包类型创建页面。
 */
class CreateWalletType extends CreateRecord
{
    protected static string $resource = WalletTypeResource::class;
}
