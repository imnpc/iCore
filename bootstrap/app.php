<?php

use App\Http\Middleware\AcceptHeaderJson;
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
        $middleware->api(prepend: [
            AcceptHeaderJson::class, // 设置请求头 Accept 为 application/json
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
