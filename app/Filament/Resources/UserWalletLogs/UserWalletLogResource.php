<?php

namespace App\Filament\Resources\UserWalletLogs;

use App\Filament\Resources\UserWalletLogs\Pages\ListUserWalletLogs;
use App\Filament\Resources\UserWalletLogs\Tables\UserWalletLogsTable;
use App\Models\UserWalletLog;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class UserWalletLogResource extends Resource implements Translateable, HasShieldPermissions
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
        ];
    }

    protected static ?string $model = UserWalletLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return UserWalletLogsTable::configure($table);
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
            'index' => ListUserWalletLogs::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
}
