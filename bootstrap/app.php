<?php

use App\Exceptions\ApiExceptionHandler;
use App\Http\Middleware\AcceptHeaderJson;
use App\Http\Middleware\ForbidBannedUser;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', [SetLocale::class]);

        // 无需验证 csrf 的接口
        $middleware->validateCsrfTokens(except: [
            'wechat',
            '*/alipay/notify',
            '*/wechat/notify',
        ]);
        // 别名供已认证路由在 auth:sanctum 之后二次校验封禁态。
        // 全局分组中间件运行在路由级 auth 之前，无法看到 Sanctum 令牌用户，
        // 因此已认证接口必须显式追加 ForbidBannedUser 到 auth:sanctum 之后。
        $middleware->alias([
            'forbid.banned' => ForbidBannedUser::class,
        ]);
        // API 需要在语言解析前启动会话，以支持会话语言偏好。
        // 保留全局封禁检查以兼容会话守卫（admin/web）与测试中的 actingAs，
        // 真实令牌用户的封禁拦截由各受保护路由的 forbid.banned 承担。
        $middleware->prependToGroup('api', [
            StartSession::class,
            SetLocale::class,
            AcceptHeaderJson::class,
            ForbidBannedUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 使用专门的API异常处理器处理API路由的异常
        $exceptions->render(function (Throwable $e, $request) {
            // 只在 API 路由中使用 JSON 响应格式
            if ($request->is('api/*')) {
                return app(ApiExceptionHandler::class)->handle($e);
            }
        });
    })->create();
