<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Clusters\Finance\Resources\UserWalletLogs\Tables\UserWalletLogsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserWalletLogRelationManager extends RelationManager
{
    protected static string $relationship = 'userWalletLog';

    protected static string|null|\BackedEnum $icon = 'heroicon-o-rectangle-stack';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament-model.label.user_wallet_log.label');
    }

    public function table(Table $table): Table
    {
        return UserWalletLogsTable::configure($table);
    }
}
