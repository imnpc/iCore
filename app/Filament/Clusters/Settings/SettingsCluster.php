<?php

namespace App\Filament\Clusters\Settings;

use BackedEnum;
use Filament\Clusters\Cluster;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SettingsCluster extends Cluster
{
    use HasPageShield;

    /**
     * 面包屑
     */
    public static function getClusterBreadcrumb(): string
    {
        return __('filament-model.navigation_group.setting.name');
    }

    /**
     * 导航图标
     *
     * @return BackedEnum|Heroicon|Htmlable|string|null
     */
    public static function getNavigationIcon(): BackedEnum|Htmlable|string|null
    {
        return Heroicon::Cog6Tooth;
    }

    /**
     * 导航标签
     */
    public static function getNavigationLabel(): string
    {
        return __('filament-model.navigation_group.setting.name');
    }

    /**
     * 导航排序
     */
    public static function getNavigationSort(): ?int
    {
        return 40;
    }

    /**
     * 标题
     */
    public function getTitle(): string
    {
        return __('filament-model.navigation_group.setting.name');
    }
}
