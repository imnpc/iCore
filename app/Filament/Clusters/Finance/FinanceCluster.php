<?php

namespace App\Filament\Clusters\Finance;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class FinanceCluster extends Cluster
{
    use HasPageShield;

    /**
     * 面包屑
     */
    public static function getClusterBreadcrumb(): string
    {
        return __('filament-model.navigation_group.finance.name');
    }

    /**
     * 导航图标
     *
     * @return BackedEnum|Heroicon|Htmlable|string|null
     */
    public static function getNavigationIcon(): BackedEnum|Htmlable|string|null
    {
        return Heroicon::BuildingLibrary;
    }

    /**
     * 导航标签
     */
    public static function getNavigationLabel(): string
    {
        return __('filament-model.navigation_group.finance.name');
    }

    /**
     * 导航排序
     */
    public static function getNavigationSort(): ?int
    {
        return 90;
    }

    /**
     * 标题
     */
    public function getTitle(): string
    {
        return __('filament-model.navigation_group.finance.name');
    }
}
