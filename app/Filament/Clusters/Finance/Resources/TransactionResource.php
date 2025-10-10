<?php

namespace App\Filament\Clusters\Finance\Resources;

use App\Filament\Clusters\Finance\FinanceCluster;
use App\Filament\Clusters\Finance\Resources\Transactions\Pages\ListTransactions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;
use TomatoPHP\FilamentWallet\Models\Transaction;

class TransactionResource extends Resource implements Translateable
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = FinanceCluster::class;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(trans('filament-model.general.id')),
                TextColumn::make('payable.id')
                    ->label(trans('filament-model.general.user_id'))
                    ->searchable(),
                TextColumn::make('payable.name')
                    ->label(trans('filament-model.general.user'))
                    ->searchable(),
                TextColumn::make('wallet.name')
                    ->label(trans('filament-wallet::messages.transactions.columns.wallet'))
                    ->numeric(),
                TextColumn::make('type')
                    ->label(trans('filament-model.general.type'))
                    ->formatStateUsing(fn (string $state): string => __("filament-wallet::messages.transactions.columns.{$state}"))
                    ->badge()
                    ->color(fn (Transaction $transaction) => $transaction->type === 'deposit' ? 'success' : 'danger'),
                TextColumn::make('amount')
                    ->label(trans('filament-wallet::messages.transactions.columns.amount'))
                    ->formatStateUsing(fn (Transaction $transaction) => (int) $transaction->amount / 100)
                    ->badge()
                    ->color(fn (Transaction $transaction) => $transaction->amount > 0 ? 'success' : 'danger'),
                IconColumn::make('confirmed')
                    ->label(trans('filament-wallet::messages.transactions.columns.confirmed'))
                    ->boolean(),
                TextColumn::make('uuid')
                    ->label(trans('filament-wallet::messages.transactions.columns.uuid'))
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label(trans('filament-model.general.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters(filament('filament-wallet')->useAccounts ? [
                SelectFilter::make('payable_id')
                    ->label(trans('filament-wallet::messages.transactions.filters.accounts'))
                    ->searchable()
                    ->options(fn () => config('filament-accounts.model')::query()->pluck('name', 'id')->toArray())
            ] : []);
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
            'index' => ListTransactions::route('/'),
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
     * 排序
     * @return int|null
     */
    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

}
