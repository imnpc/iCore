<?php

namespace App\Filament\Clusters\Finance\Resources\WalletTypes\Tables;

use App\Models\WalletType;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class WalletTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(trans('filament-model.general.id'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(trans('filament-model.general.name'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(WalletType::transAttribute('slug'))
                    ->searchable(),
                TextColumn::make('description')
                    ->label(WalletType::transAttribute('description'))
                    ->searchable(),
                TextColumn::make('decimal_places')
                    ->label(WalletType::transAttribute('decimal_places'))
                    ->numeric(),
                ImageColumn::make('icon')
                    ->label(WalletType::transAttribute('icon')),
                TextColumn::make('sort')
                    ->label(trans('filament-model.general.sort'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_enabled')
                    ->label(trans('filament-model.general.is_enabled'))
                    ->boolean(),
//                TextColumn::make('created_at')
//                    ->label(WalletType::transAttribute('created_at'))
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(trans('filament-model.general.updated_at'))
                    ->dateTime(),
//                TextColumn::make('deleted_at')
//                    ->label(WalletType::transAttribute('deleted_at'))
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make(),
                ])->visible(fn ($record): bool => $record->id > 2),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
//                    ForceDeleteBulkAction::make(),
//                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
