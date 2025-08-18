<?php

namespace App\Filament\Clusters\Settings;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class SettingsCluster extends Cluster
{
    /**
     * 面包屑
     * @return string
     */
    public static function getClusterBreadcrumb(): string
    {
        return __('filament-model.navigation_group.setting.name');
    }

    /**
     * 导航图标
     * @return BackedEnum|Heroicon|\Illuminate\Contracts\Support\Htmlable|string|null
     */
    public static function getNavigationIcon(): BackedEnum|\Illuminate\Contracts\Support\Htmlable|string|null
    {
        return Heroicon::Cog6Tooth;
    }

    /**
     * 导航标签
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('filament-model.navigation_group.setting.name');
    }

    /**
     * 导航排序
     * @return int|null
     */
    public static function getNavigationSort(): ?int
    {
        return 100;
    }
}
