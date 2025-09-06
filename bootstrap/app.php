<?php

use App\Http\Middleware\AcceptHeaderJson;
use App\Traits\MakesApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // 使用 MakesApiResponses 的 error 渲染异常
        $exceptions->render(function (Throwable $e, $request) {
            // 只在 API 路由中使用 JSON 响应格式
            if ($request->is('api/*')) {
                // 创建一个匿名类使用 MakesApiResponses trait
                $responseHandler = new class {
                    use MakesApiResponses;
                };

                // 根据异常类型返回不同的错误响应
                switch (true) {
                    case $e instanceof ValidationException:
                        return $responseHandler::error(
                            $e->getMessage() ?: '请求参数验证失败',
                            422,
                            $e->errors()
                        );

                    case $e instanceof AuthenticationException:
                        return $responseHandler::error(
                            '认证失败，请重新登录',
                            401
                        );

                    case $e instanceof AuthorizationException:
                        return $responseHandler::error(
                            '权限不足，拒绝访问',
                            403
                        );

                    case $e instanceof ModelNotFoundException:
                        return $responseHandler::error(
                            '请求的资源不存在',
                            404
                        );

                    case $e instanceof NotFoundHttpException:
                        return $responseHandler::error(
                            '请求的路由不存在',
                            404
                        );

                    case $e instanceof HttpException:
                        return $responseHandler::error(
                            $e->getMessage() ?: '服务器错误',
                            $e->getStatusCode()
                        );

                    default:
                        // 在生产环境隐藏具体错误信息
                        $message = app()->environment('production')
                            ? '服务器内部错误'
                            : $e->getMessage();

                        return $responseHandler::error(
                            $message,
                            500
                        );
                }
            }
        });
    })->create();
