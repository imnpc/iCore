<?php

namespace App\Filament\Clusters\Finance\Resources\Transactions\Pages;

use App\Filament\Clusters\Finance\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
