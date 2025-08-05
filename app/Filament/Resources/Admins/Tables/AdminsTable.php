<?php

namespace App\Filament\Resources\Admins\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
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
            ])
            ->striped() // 斑马纹
            ->filters([
//                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make(),
                ])->visible(fn ($record): bool => $record->id > 1),
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
