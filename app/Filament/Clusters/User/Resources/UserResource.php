<?php

namespace App\Filament\Clusters\User\Resources;

use App\Filament\Clusters\Finance\FinanceCluster;
use App\Filament\Clusters\User\Resources\Users\Pages\CreateUser;
use App\Filament\Clusters\User\Resources\Users\Pages\EditUser;
use App\Filament\Clusters\User\Resources\Users\Pages\ListUsers;
use App\Filament\Clusters\User\Resources\Users\RelationManagers\UserWalletLogRelationManager;
use App\Filament\Clusters\User\Resources\Users\RelationManagers\WalletRelationManager;
use App\Filament\Clusters\User\Resources\Users\Schemas\UserForm;
use App\Filament\Clusters\User\Resources\Users\Tables\UsersTable;
use App\Filament\Clusters\User\UserCluster;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class UserResource extends Resource implements Translateable, HasShieldPermissions
{
    use HasTranslateableResources;
    use HasShieldFormComponents;

    protected static ?string $translateablePackageKey = '';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    protected static ?string $cluster = UserCluster::class;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

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
    public static function getNavigationGroup(): ?string
    {
        return __('filament-model.navigation_group.user.name');
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
