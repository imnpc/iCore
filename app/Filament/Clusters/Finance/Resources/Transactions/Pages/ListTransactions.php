<?php

namespace App\Filament\Clusters\Finance\Resources\Transactions\Pages;

use App\Filament\Clusters\Finance\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;

/**
 * 交易记录列表页面。
 */
class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
