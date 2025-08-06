<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Actions\CreateAction;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables\Table;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Maggomann\FilamentModelTranslator\FilamentModelTranslatorServicePlugin;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // 设置默认时区
        FilamentTimezone::set('Asia/Shanghai');
        // 设置表格默认时间格式
        Table::configureUsing(function (Table $table) {
            $table->defaultDateDisplayFormat('Y-m-d');
            $table->defaultDateTimeDisplayFormat('Y-m-d H:i:s');
        });
        // 默认关闭 创建另一个按钮
        CreateRecord::disableCreateAnother();
        CreateAction::configureUsing(fn(CreateAction $action) => $action->createAnother(false));
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin') // 自定义认证 guard
            ->login(Login::class) // 自定义登录页面
            ->colors([
//                'primary' => Color::Amber,
                'primary' => Color::Blue,
            ])
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm'      => 2,
                    ]), // 权限
                FilamentSettingsPlugin::make()
                    ->pages([
                        \App\Filament\Pages\Settings\Settings::class,
                    ]), // 系统设置
                FilamentModelTranslatorServicePlugin::make(), //  模型翻译
//                ActivitylogPlugin::make(), // 记录日志
                FilamentAuthenticationLogPlugin::make(), // 登录日志
                EnvironmentIndicatorPlugin::make()
                    ->color(fn() => match (app()->environment()) {
                        'production' => Color::Green,
                        'staging' => Color::Orange,
                        'local' => Color::Red,
                        default => Color::Blue,
                    }), // 运行环境
                EasyFooterPlugin::make()
                    ->withLoadTime('Processed in '), // 页脚
            ])
            ->profile()
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->regenerableRecoveryCodes(false),
            ])
            ->resourceEditPageRedirect('index') // 修改编辑页面重定向
            ->resourceCreatePageRedirect('index') // 创建页面重定向
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
