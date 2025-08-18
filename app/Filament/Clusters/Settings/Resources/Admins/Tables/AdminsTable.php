<?php

namespace App\Filament\Clusters\Settings\Resources\Admins\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Widiu7omo\FilamentBandel\Actions\BanAction;
use Widiu7omo\FilamentBandel\Actions\UnbanAction;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class AdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(trans('filament-model.general.id')),
                TextColumn::make('name')
                    ->label(trans('filament-model.general.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(trans('filament-model.general.email'))
                    ->searchable(),
                PhoneColumn::make('mobile')
                    ->label(trans('filament-model.general.mobile'))
                    ->displayFormat(PhoneInputNumberType::NATIONAL),
                ImageColumn::make('avatar')
                    ->label(trans('filament-model.general.avatar'))
                    ->visibility('public')
                    ->circular(),
                IconColumn::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(trans('filament-model.general.updated_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('banned_at')
                    ->label(trans('filament-model.general.banned_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->striped() // 斑马纹
            ->filters([
//                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    BanAction::make(__('filament-bandel::translations.ban_model'))->color('warning'),
                    UnbanAction::make(__('filament-bandel::translations.unban_model'))->color('success'),
                    DeleteAction::make(),
                ])->visible(fn ($record): bool => $record->id > 1),
//                ActivityLogTimelineTableAction::make('Activities'),
//                WalletAction::make('money'),
            ])
            ->toolbarActions([
//                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
//                    ForceDeleteBulkAction::make(),
//                    RestoreBulkAction::make(),
//                ]),
            ]);
    }
}
