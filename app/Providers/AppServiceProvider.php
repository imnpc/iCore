<?php

namespace App\Providers;

use App\Models\Admin;
use App\Services\ApiDocsTagGrouper;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\RouteInfo;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Passkeys\Passkeys;
use ReflectionMethod;

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

        // filament 加载自定义 css
        FilamentAsset::register([
            Css::make('custom-css', __DIR__.'/../../resources/css/custom.css'),
        ]);

        // 循环处理监听事件
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        // 自动发现策略文件
        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass).'Policy';
        });

        // 插件需要手动注册策略，后台角色才能管理
        //        Gate::policy(Activity::class, ActivityPolicy::class); // 操作日志单独的策略文件

        // 更改权限生成规则 Change permission generation rules
        FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject, string $case, string $separator) {
            return str($affix)->camel().'_'.str($subject)->pascal();
        });

        // 自动配置 swagger 文档
        $apiDocsTagGrouper = app(ApiDocsTagGrouper::class);

        Scramble::configure()
            ->withOperationTransformers(
                fn (Operation $operation, RouteInfo $routeInfo) => $this->addAcceptLanguageParameter($operation)
            );

        Scramble::resolveTagsUsing(
            function (
                RouteInfo $routeInfo,
                Operation $operation
            ) use ($apiDocsTagGrouper): array {
                return [
                    $apiDocsTagGrouper->makeHierarchicalTag(
                        $this->resolveScrambleChildTag($routeInfo),
                        $routeInfo->route->uri(),
                    ),
                ];
            }
        );

        // filament 多语言切换
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'zh_CN'])
                ->labels([
                    'en' => 'English',
                    'zh_CN' => '简体中文',
                ])
                ->flags([
                    'en' => asset('vendor/blade-country-flags/1x1-us.svg'),
                    'zh_CN' => asset('vendor/blade-country-flags/1x1-cn.svg'),
                ])
                ->circular();
        });

        // passkey 关联模型指向 Admin
        Passkeys::useUserModel(Admin::class);
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

    private function resolveScrambleChildTag(RouteInfo $routeInfo): string
    {
        $reflection = $routeInfo->reflectionAction();

        $methodClassGroupAttributes = $reflection instanceof ReflectionMethod
            ? $reflection->getDeclaringClass()->getAttributes(Group::class)
            : [];

        $groupAttributes = [
            ...($reflection?->getAttributes(Group::class) ?? []),
            ...$methodClassGroupAttributes,
        ];

        foreach ($groupAttributes as $groupAttribute) {
            $name = trim((string) $groupAttribute->newInstance()->name);

            if ($name !== '') {
                return $name;
            }
        }

        $fallbackName = trim((string) Str::of(class_basename($routeInfo->className()))->replace('Controller', ''));

        return $fallbackName !== '' ? $fallbackName : '其他';
    }

    protected function addAcceptLanguageParameter(Operation $operation): void
    {
        $exists = collect($operation->parameters)
            ->contains(
                fn (Parameter $parameter) => strtolower($parameter->name) === 'accept-language'
                    && $parameter->in === 'header'
            );

        if ($exists) {
            return;
        }

        $operation->parameters[] = Parameter::make(
            'Accept-Language',
            'header'
        )
            ->description('请求语言，例如：zh_CN、en')
            ->required(false)
            ->setSchema(
                Schema::fromType(new StringType)
            )
            ->example('zh_CN');
    }
}
