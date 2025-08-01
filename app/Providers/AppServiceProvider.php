<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
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

        // 多语言切换
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en','zh_CN','zh_TW'])
                ->labels([
                    'en' => 'English',
                    'zh_CN' => '简体中文',
                    'zh_TW' => '繁體中文',
                ])
//                ->flags([
//                    'en' => asset('vendor/blade-country-flags/1x1-us.svg'),
//                    'zh_CN' => asset('vendor/blade-country-flags/1x1-cn.svg'),
//                    'zh_TW' => asset('vendor/blade-country-flags/1x1-hk.svg'),
//                ])
                ->circular();
        });
    }
}
