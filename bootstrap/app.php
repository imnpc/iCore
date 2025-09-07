<?php

use App\Http\Middleware\AcceptHeaderJson;
use App\Exceptions\ApiExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 无需验证 csrf 的接口
        $middleware->validateCsrfTokens(except: [
            'wechat',
            '*/alipay/notify',
            '*/wechat/notify',
        ]);
        // 在 API 中间件组最前面添加 JSON 请求头强制设置
        $middleware->prependToGroup('api', [
            AcceptHeaderJson::class, // 设置请求头 Accept 为 application/json
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
