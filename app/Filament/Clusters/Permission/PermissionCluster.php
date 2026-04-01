<?php

namespace App\Filament\Clusters\Permission;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PermissionCluster extends Cluster
{
    use HasPageShield;

    /**
     * 面包屑
     */
    public static function getClusterBreadcrumb(): string
    {
        return __('filament-model.navigation_group.permission.name');
    }

    /**
     * 导航图标
     *
     * @return BackedEnum|Heroicon|Htmlable|string|null
     */
    public static function getNavigationIcon(): BackedEnum|Htmlable|string|null
    {
        return Heroicon::ShieldCheck;
    }

    /**
     * 导航标签
     */
    public static function getNavigationLabel(): string
    {
        return __('filament-model.navigation_group.permission.name');
    }

    /**
     * 导航排序
     */
    public static function getNavigationSort(): ?int
    {
        return 100;
    }

    /**
     * 标题
     */
    public function getTitle(): string
    {
        return __('filament-model.navigation_group.permission.name');
    }
}
