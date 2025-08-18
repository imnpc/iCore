<?php

namespace App\Filament\Clusters\Finance\Resources\UserWalletLogs\Tables;

use App\Models\UserWalletLog;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserWalletLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(trans('filament-model.general.id'))
                    ->searchable(),
                TextColumn::make('user_id')
                    ->label(trans('filament-model.general.user_id')),
                TextColumn::make('user.name')
                    ->label(trans('filament-model.general.user')),
                TextColumn::make('walletType.name')
                    ->label(UserWalletLog::transAttribute('walletType')),
                TextColumn::make('day')
                    ->label(trans('filament-model.general.day'))
                    ->date()
                    ->sortable(),
                TextColumn::make('old')
                    ->label(UserWalletLog::transAttribute('old'))
                    ->numeric(),
                TextColumn::make('add')
                    ->label(UserWalletLog::transAttribute('add'))
                    ->numeric(),
                TextColumn::make('new')
                    ->label(UserWalletLog::transAttribute('new'))
                    ->numeric(),
                TextColumn::make('from')
                    ->label(UserWalletLog::transAttribute('from')),
                TextColumn::make('remark')
                    ->label(UserWalletLog::transAttribute('remark')),
                TextColumn::make('fromUser.name')
                    ->label(UserWalletLog::transAttribute('fromUser')),
                TextColumn::make('order_id')
                    ->label(trans('filament-model.general.order_id')),
                TextColumn::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->dateTime(),
            ])
            ->filters([
                // 搜索用户ID
                Filter::make('user_id')
                    ->schema([
                        TextInput::make('user_id')
                            ->label(trans('filament-model.general.user_id')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if($data['user_id'] > 0){
                            return $query->where('user_id', '=', $data['user_id']);
                        }
                    }),
                SelectFilter::make('wallet_type_id')
                    ->label(UserWalletLog::transAttribute('walletType'))
                    ->relationship('walletType', 'name'),
//                TrashedFilter::make(),
            ])
            ->recordActions([
//                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
//                    ForceDeleteBulkAction::make(),
//                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc'); // 默认排序
    }
}
