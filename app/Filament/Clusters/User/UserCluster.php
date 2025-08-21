<?php

namespace App\Filament\Clusters\User;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class UserCluster extends Cluster
{
    use HasPageShield;

    /**
     * 面包屑
     * @return string
     */
    public static function getClusterBreadcrumb(): string
    {
        return __('filament-model.navigation_group.user.name');
    }

    /**
     * 导航图标
     * @return BackedEnum|Heroicon|\Illuminate\Contracts\Support\Htmlable|string|null
     */
    public static function getNavigationIcon(): BackedEnum|\Illuminate\Contracts\Support\Htmlable|string|null
    {
        return Heroicon::UserCircle;
    }

    /**
     * 导航标签
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('filament-model.navigation_group.user.name');
    }

    /**
     * 导航排序
     * @return int|null
     */
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    /**
     * 标题
     * @return string
     */
    public function getTitle(): string
    {
        return __('filament-model.navigation_group.user.name');
    }
}
