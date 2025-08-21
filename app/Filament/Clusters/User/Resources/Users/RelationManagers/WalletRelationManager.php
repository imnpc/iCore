<?php

namespace App\Filament\Clusters\User\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentWallet\Filament\Resources\WalletResource;

class WalletRelationManager extends RelationManager
{
    protected static string $relationship = 'wallets';
    protected static string|null|\BackedEnum $icon = 'heroicon-o-wallet';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament-model.label.wallet.label');
    }

    public function table(Table $table): Table
    {
        return WalletResource::table($table);
    }
}
