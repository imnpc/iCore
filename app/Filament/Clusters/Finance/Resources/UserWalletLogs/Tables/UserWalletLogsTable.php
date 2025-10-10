<?php

namespace App\Filament\Clusters\Finance\Resources\UserWalletLogs\Tables;

use App\Enums\FromType;
use App\Models\UserWalletLog;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
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
                TextColumn::make('type_text')
                    ->label(trans('filament-model.general.type'))
                    ->badge()
                    ->color(function (UserWalletLog $record) {
                        return $record->add > 0 ? 'success' : 'danger';
                    }),
                TextColumn::make('old')
                    ->label(UserWalletLog::transAttribute('old'))
                    ->numeric(),
                TextColumn::make('add')
                    ->label(UserWalletLog::transAttribute('add'))
                    ->numeric()
                    ->badge()
                    ->color(function (UserWalletLog $record) {
                        return $record->add > 0 ? 'success' : 'danger';
                    }),
                TextColumn::make('new')
                    ->label(UserWalletLog::transAttribute('new'))
                    ->numeric(),
                TextColumn::make('from')
                    ->label(UserWalletLog::transAttribute('from')),
                TextColumn::make('remark')
                    ->label(UserWalletLog::transAttribute('remark')),
                // from_user_id
                TextColumn::make('from_user_id')
                    ->label(UserWalletLog::transAttribute('fromUser')),

//                TextColumn::make('fromUser.name')
//                    ->label(UserWalletLog::transAttribute('fromUser')),
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
                        if ($data['user_id'] > 0) {
                            return $query->where('user_id', '=', $data['user_id']);
                        }
                    }),
                SelectFilter::make('wallet_type_id')
                    ->label(UserWalletLog::transAttribute('walletType'))
                    ->relationship('walletType', 'name'),
                // 订单 ID
                Filter::make('order_id')
                    ->schema([
                        TextInput::make('order_id')
                            ->label(trans('filament-model.general.order_id')),

                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['order_id'] > 0) {
                            return $query->where('order_id', '=', $data['order_id']);
                        }
                    }),
                // 来源
                SelectFilter::make('from')
                    ->label('来源')
                    ->options(FromType::class),
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
