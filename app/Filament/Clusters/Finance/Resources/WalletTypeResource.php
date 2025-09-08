<?php

namespace App\Filament\Clusters\Finance\Resources;

use App\Filament\Clusters\Finance\FinanceCluster;
use App\Filament\Clusters\Finance\Resources\WalletTypes\Pages\CreateWalletType;
use App\Filament\Clusters\Finance\Resources\WalletTypes\Pages\EditWalletType;
use App\Filament\Clusters\Finance\Resources\WalletTypes\Pages\ListWalletTypes;
use App\Filament\Clusters\Finance\Resources\WalletTypes\Schemas\WalletTypeForm;
use App\Filament\Clusters\Finance\Resources\WalletTypes\Tables\WalletTypesTable;
use App\Models\WalletType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class WalletTypeResource extends Resource implements Translateable
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = WalletType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    // 集群
    protected static ?string $cluster = FinanceCluster::class;

    public static function form(Schema $schema): Schema
    {
        return WalletTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WalletTypesTable::configure($table);
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
            'index' => ListWalletTypes::route('/'),
            'create' => CreateWalletType::route('/create'),
            'edit' => EditWalletType::route('/{record}/edit'),
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
