<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 自动发现策略文件
        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass) . 'Policy';
        });
        // 权限管理 使用模型名 不使用 :: 间隔区分
        FilamentShield::configurePermissionIdentifierUsing(
            fn($resource) => str($resource::getModel())
                ->afterLast('\\')
                ->toString()
        );
    }
}
