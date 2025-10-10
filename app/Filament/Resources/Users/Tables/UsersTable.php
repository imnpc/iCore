<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Actions\WalletAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use Widiu7omo\FilamentBandel\Actions\BanAction;
use Widiu7omo\FilamentBandel\Actions\UnbanAction;
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
                TextColumn::make('parent_id')
                    ->label(trans('filament-model.general.parent_id')),
                ImageColumn::make('avatar_url')
                    ->label(trans('filament-model.general.avatar_url'))
                    ->circular(),
                IconColumn::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->boolean(),
//                TextColumn::make('last_login_at')
//                    ->label(User::transAttribute('last_login_at'))
//                    ->dateTime()
//                    ->sortable(),
//                TextColumn::make('last_login_ip')
//                    ->label(User::transAttribute('last_login_ip'))
//                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->dateTime(),
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
                Filter::make('parent_id')
                    ->schema([
                        TextInput::make('parent_id')
                            ->label(trans('filament-model.general.parent_id'))
                            ->numeric(), // 添加数字验证
                    ])
                    ->query(function (Builder $query, array $data) {
                        // 更完善的检查逻辑
                        if (isset($data['parent_id']) && $data['parent_id'] !== null && $data['parent_id'] !== '') {
                            return $query->where('parent_id', '=', $data['parent_id']);
                        }
                        return $query;
                    }),

                SelectFilter::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->options([
                        1 => '启用',
                        0 => '禁用',
                    ]),
                 SelectFilter::make('tags')
                     ->label(trans('filament-model.general.tags'))
                     ->relationship('tags', 'name')
                     ->multiple(),
                // 日期筛选
                Filter::make('created_at')
                    ->label('创建时间')
                    ->schema([
                        DatePicker::make('created_from')->label('开始时间'),
                        DatePicker::make('created_until')->label('结束时间'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                WalletAction::make(),
                ActionGroup::make([
                    BanAction::make(__('filament-bandel::translations.ban_model'))->color('warning'),
                    UnbanAction::make(__('filament-bandel::translations.unban_model'))->color('success'),
                    DeleteAction::make()->color('danger'),
                ]),
            ])
            ->headerActions([
                ExportAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
                ]),
//                ExportBulkAction::make(),
            ]);
    }
}
