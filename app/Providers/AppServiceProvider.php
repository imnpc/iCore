<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\RouteInfo;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        // filament 表格默认附加时间列(有 bug 未生效)
        Table::configureUsing(function (Table $table) {
            $table
                ->pushColumns([
                    TextColumn::make('created_at')
                        ->label(trans('filament-model.general.created_at'))
                        ->dateTime()
                        ->sortable(),
                    TextColumn::make('updated_at')
                        ->label(trans('filament-model.general.updated_at'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]);
        });

        // 循环处理监听事件
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        // 自动发现策略文件
        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass) . 'Policy';
        });

        // 插件需要手动注册策略，后台角色才能管理
//        Gate::policy(Activity::class, ActivityPolicy::class); // 操作日志单独的策略文件

        // filament 权限管理 使用模型名 不使用 :: 间隔区分
        FilamentShield::configurePermissionIdentifierUsing(
            fn($resource) => str($resource::getModel())
                ->afterLast('\\')
                ->toString()
        );

        // 自动配置 swagger 文档
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            })
            ->withOperationTransformers(function (Operation $operation, RouteInfo $routeInfo) {
                $routeMiddleware = $routeInfo->route->gatherMiddleware();

                $hasAuthMiddleware = collect($routeMiddleware)->contains(
                    fn ($m) => Str::startsWith($m, 'auth:')
                );

                if (! $hasAuthMiddleware) {
                    $operation->security = [];
                }
            });

        // filament 多语言切换
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en','zh_CN','zh_TW'])
                ->labels([
                    'en' => 'English',
                    'zh_CN' => '简体中文',
                    'zh_TW' => '繁體中文',
                ])
                ->flags([
                    'en' => asset('vendor/blade-country-flags/1x1-us.svg'),
                    'zh_CN' => asset('vendor/blade-country-flags/1x1-cn.svg'),
                    'zh_TW' => asset('vendor/blade-country-flags/1x1-hk.svg'),
                ])
                ->circular();
        });
    }

    // 事件列表
    private $listen = [
        // access_token 生成以后清除旧的 token ，然后记录登录时间和日期
//        'Laravel\Passport\Events\AccessTokenCreated' => [
//            'App\Listeners\RevokeOldTokens',
//            'App\Listeners\LogSuccessfulLogin',
//        ],
        // refresh_token 生成以后删除已吊销的 token
//        'Laravel\Passport\Events\RefreshTokenCreated' => [
//            'App\Listeners\PruneOldTokens',
//        ],
    ];
}
