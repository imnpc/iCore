<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class UsersTable
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
                TextColumn::make('email')
                    ->label(trans('filament-model.general.email'))
                    ->searchable(),
                PhoneColumn::make('mobile')
                    ->label(trans('filament-model.general.mobile'))
                    ->displayFormat(PhoneInputNumberType::NATIONAL)
                    ->searchable(),
                ImageColumn::make('avatar_url')
                    ->label(trans('filament-model.general.avatar_url'))
                    ->circular(),
                IconColumn::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->boolean(),
                TextColumn::make('last_login_at')
                    ->label(User::transAttribute('last_login_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_login_ip')
                    ->label(User::transAttribute('last_login_ip'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(trans('filament-model.general.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('banned_at')
                    ->label(trans('filament-model.general.banned_at'))
                    ->dateTime()
                    ->sortable(),
                SpatieTagsColumn::make('tags')
                    ->label(trans('filament-model.general.tags')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(User::transAttribute('status'))
                    ->options([
                        1 => '启用',
                        0 => '禁用',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
