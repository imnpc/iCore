<?php

namespace App\Filament\Clusters\Finance\Resources;

use App\Filament\Clusters\Finance\FinanceCluster;
use App\Filament\Clusters\Finance\Resources\Wallets\Pages\ListWallets;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;
use TomatoPHP\FilamentWallet\Models\Wallet;

class WalletResource extends Resource  implements Translateable
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = Wallet::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $cluster = FinanceCluster::class;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(trans('filament-model.general.id')),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->label(trans('filament-wallet::messages.wallets.columns.created_at'))
//                    ->dateTime(),
                TextColumn::make('holder.id')
                    ->label(trans('filament-model.general.user_id'))
                    ->searchable(),
                TextColumn::make('holder.name')
                    ->label(trans('filament-model.general.user'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(trans('filament-model.general.name'))
                    ->searchable(),
                TextColumn::make('balanceFloatNum')
                    ->label(trans('filament-wallet::messages.wallets.columns.balance'))
                    ->badge()
                    ->numeric(2),
                TextColumn::make('uuid')
                    ->label(trans('filament-wallet::messages.wallets.columns.uuid'))
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label(trans('filament-model.general.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters(
                filament('filament-wallet')->useAccounts ? [
                    SelectFilter::make('holder_id')
                        ->label(trans('filament-wallet::messages.wallets.filters.accounts'))
                        ->searchable()
                        ->options(fn() => config('filament-accounts.model')::query()->pluck('name', 'id')->toArray())
                ] : [
                    SelectFilter::make('holder_id')
                        ->label(trans('filament-model.general.user_id'))
                        ->options(User::query()->pluck('email', 'id')->toArray())
                        ->searchable()
                        ->preload()
                ],
            )
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWallets::route('/'),
        ];
    }

    /**
     * 导航组
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return __('filament-model.navigation_group.wallet.name');
    }

    /**
     * 导航徽章
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * 排序
     * @return int|null
     */
    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
