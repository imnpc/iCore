<?php

namespace App\Filament\Clusters\Permission\Resources;

use App\Filament\Clusters\Permission\PermissionCluster;
use App\Filament\Clusters\Permission\Resources\Admins\Pages\CreateAdmin;
use App\Filament\Clusters\Permission\Resources\Admins\Pages\EditAdmin;
use App\Filament\Clusters\Permission\Resources\Admins\Pages\ListAdmins;
use App\Filament\Clusters\Permission\Resources\Admins\Schemas\AdminForm;
use App\Filament\Clusters\Permission\Resources\Admins\Tables\AdminsTable;
use App\Models\Admin;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class AdminResource extends Resource implements Translateable
{
    // 添加翻译
    use HasShieldFormComponents;
    use HasTranslateableResources; // 添加表单组件权限

    /**
     * 翻译
     */
    protected static ?string $translateablePackageKey = '';

    /**
     * 集群
     */
    protected static ?string $cluster = PermissionCluster::class;

    /**
     * 标题
     */
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $model = Admin::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Schema $schema): Schema
    {
        return AdminForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //            ActivitylogRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmins::route('/'),
            'create' => CreateAdmin::route('/create'),
            'edit' => EditAdmin::route('/{record}/edit'),
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
     */
    public static function getNavigationGroup(): ?string
    {
        return __('filament-model.navigation_group.role.name');
    }

    /**
     * 导航徽章
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * 排序
     */
    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
