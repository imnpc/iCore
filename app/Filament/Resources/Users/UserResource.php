<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\RelationManagers\UserWalletLogRelationManager;
use App\Filament\Resources\Users\RelationManagers\WalletRelationManager;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class UserResource extends Resource implements Translateable
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationGroup::make(trans('filament-model.label.wallet.label'), [
                UserWalletLogRelationManager::class, // 钱包日志
                WalletRelationManager::class, // 钱包
            ])->icon('heroicon-o-wallet'),
            RelationGroup::make(trans('filament-authentication-log::filament-authentication-log.navigation.authentication-log.label'), [
                \App\Filament\RelationManagers\AuthenticationLogsRelationManager::class,
            ])->icon('heroicon-o-shield-check'),

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * 导航组
     * @return string|null
     */
//    public static function getNavigationGroup(): ?string
//    {
//        return __('filament-model.navigation_group.user.name');
//    }

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
        return 1;
    }

    // 搜索字段
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'mobile'];
    }

    // 搜索结果标题
    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->name." / ".$record->email." / ".$record->mobile;
    }
}
