<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\MakesApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

/**
 * Laravel 12 优化版 API 异常处理器
 *
 * 提供统一的 API 异常处理，包含：
 * - 异常分类处理
 * - 日志记录
 * - 开发/生产环境差异化处理
 * - 错误追踪和调试信息
 */
class ApiExceptionHandler extends ExceptionHandler
{
    use MakesApiResponses;

    /**
     * 异常配置映射表
     */
    private const EXCEPTION_CONFIG = [
        ValidationException::class              => [
            'message'        => '请求参数验证失败',
            'status'         => 422,
            'level'          => 'warning',
            'include_errors' => true,
        ],
        AuthenticationException::class          => [
            'message' => '认证失败，请重新登录',
            'status'  => 401,
            'level'   => 'warning',
        ],
        AuthorizationException::class           => [
            'message' => '权限不足，拒绝访问',
            'status'  => 403,
            'level'   => 'warning',
        ],
        ModelNotFoundException::class           => [
            'message' => '请求的资源不存在',
            'status'  => 404,
            'level'   => 'notice',
        ],
        NotFoundHttpException::class            => [
            'message' => '请求的路由不存在',
            'status'  => 404,
            'level'   => 'notice',
        ],
        MethodNotAllowedHttpException::class    => [
            'message' => '请求方法不允许',
            'status'  => 405,
            'level'   => 'warning',
        ],
        TooManyRequestsHttpException::class     => [
            'message' => '请求过于频繁，请稍后再试',
            'status'  => 429,
            'level'   => 'warning',
        ],
        ThrottleRequestsException::class        => [
            'message' => '请求过于频繁，请稍后再试',
            'status'  => 429,
            'level'   => 'warning',
        ],
        UnprocessableEntityHttpException::class => [
            'message' => '无法处理的请求实体',
            'status'  => 422,
            'level'   => 'warning',
        ],
        QueryException::class                   => [
            'message'            => '数据库查询错误',
            'status'             => 500,
            'level'              => 'error',
            'hide_in_production' => true,
        ],
    ];

    /**
     * 不需要报告的异常类型
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
        MethodNotAllowedHttpException::class,
        ThrottleRequestsException::class,
        HttpResponseException::class,
        ValidationException::class,
    ];

    /**
     * Laravel 12 渲染方法
     */
    public function render($request, Throwable $e): mixed
    {
        // 只处理 API 请求
        if ($this->isApiRequest($request)) {
            return $this->handleApiException($e, $request);
        }

        return parent::render($request, $e);
    }

    /**
     * 处理API异常并返回JSON响应
     */
    public function handleApiException(Throwable $exception, ?Request $request = null): JsonResponse
    {
        $exceptionClass = get_class($exception);

        // 记录异常日志
        $this->logException($exception, $request);

        // 处理框架返回的现成响应
        if ($exception instanceof HttpResponseException) {
            return $this->handleHttpResponseException($exception);
        }

        // 处理验证异常（特殊处理，包含详细错误信息）
        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception);
        }

        // 处理HTTP异常
        if ($exception instanceof HttpException) {
            return $this->handleHttpException($exception);
        }

        // 处理已知异常类型
        if (isset(self::EXCEPTION_CONFIG[$exceptionClass])) {
            return $this->handleKnownException($exception, self::EXCEPTION_CONFIG[$exceptionClass]);
        }

        // 处理未知异常
        return $this->handleUnknownException($exception);
    }

    /**
     * 处理验证异常
     */
    private function handleValidationException(ValidationException $exception): JsonResponse
    {
        $config = self::EXCEPTION_CONFIG[ValidationException::class];

        $response = $this->error(
            $exception->getMessage() ?: $config['message'],
            $config['status'],
            $exception->errors()
        );

        // 在非生产环境添加调试信息
        return $this->addDebugInfo($response, $exception);
    }

    /**
     * 处理HTTP异常
     */
    private function handleHttpException(HttpException $exception): JsonResponse
    {
        $message = $exception->getMessage();
        $statusCode = $exception->getStatusCode();

        // 如果没有自定义消息，使用默认消息
        if (empty($message)) {
            $message = match ($statusCode) {
                400 => '请求参数错误',
                401 => '未授权访问',
                403 => '禁止访问',
                404 => '资源不存在',
                405 => '请求方法不允许',
                409 => '资源冲突',
                429 => '请求过于频繁',
                500 => '服务器内部错误',
                502 => '网关错误',
                503 => '服务不可用',
                default => '请求处理失败'
            };
        }

        $response = $this->error($message, $statusCode, []);

        // 在非生产环境添加调试信息
        return $this->addDebugInfo($response, $exception);
    }

    /**
     * 处理已有响应的异常（如表单验证/中间件返回的响应）
     */
    private function handleHttpResponseException(HttpResponseException $exception): JsonResponse
    {
        $response = $exception->getResponse();

        // 已经是 JsonResponse 直接返回
        if ($response instanceof JsonResponse) {
            return $response;
        }

        // 不是 JSON 时，统一包装为 JSON 错误响应
        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 500;
        $content = method_exists($response, 'getContent') ? $response->getContent() : '';

        $json = $this->error(
            '请求处理失败',
            $status,
            [
                'message' => $content,
            ]
        );

        return $this->addDebugInfo($json, $exception);
    }

    /**
     * 处理已知异常类型
     */
    private function handleKnownException(Throwable $exception, array $config): JsonResponse
    {
        $message = $config['message'];

        // 在生产环境隐藏敏感信息
        if (app()->environment('production') && isset($config['hide_in_production'])) {
            $message = '服务器处理请求时出现错误';
        }

        $response = $this->error($message, $config['status'], []);

        // 在非生产环境添加调试信息
        return $this->addDebugInfo($response, $exception);
    }

    /**
     * 处理未知异常
     */
    private function handleUnknownException(Throwable $exception): JsonResponse
    {
        // 在生产环境隐藏具体错误信息
        $message = app()->environment('production')
            ? '服务器内部错误'
            : $exception->getMessage();

        $response = $this->error($message, 500, []);

        // 在非生产环境添加调试信息
        return $this->addDebugInfo($response, $exception);
    }

    /**
     * 记录异常日志
     */
    private function logException(Throwable $exception, ?Request $request = null): void
    {
        $exceptionClass = get_class($exception);
        $config = self::EXCEPTION_CONFIG[$exceptionClass] ?? ['level' => 'error'];

        $context = [
            'exception_class' => $exceptionClass,
            'file'            => $exception->getFile(),
            'line'            => $exception->getLine(),
            'trace_id'        => $this->getTraceId(),
        ];

        if ($request) {
            $context['request'] = [
                'method'     => $request->getMethod(),
                'url'        => $request->fullUrl(),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id'    => $request->user()?->id,
            ];
        }

        Log::log(
            $config['level'],
            "API异常: {$exception->getMessage()}",
            $context
        );
    }

    /**
     * 获取调试数据
     */
    private function getDebugData(Throwable $exception): ?array
    {
        if (app()->environment('production')) {
            return null;
        }

        return [
            'exception' => get_class($exception),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
            'trace_id'  => $this->getTraceId(),
            'previous'  => $exception->getPrevious()?->getMessage(),
        ];
    }

    /**
     * 判断是否为API请求
     */
    private function isApiRequest(Request $request): bool
    {
        return $request->is('api/*') ||
            $request->wantsJson() ||
            $request->expectsJson();
    }

    /**
     * 获取追踪ID
     */
    private function getTraceId(): string
    {
        return request()->header('X-Trace-ID') ?? uniqid('trace_', true);
    }

    /**
     * 为响应添加调试信息
     */
    private function addDebugInfo(JsonResponse $response, Throwable $exception): JsonResponse
    {
        if (!app()->environment('production')) {
            $responseData = $response->getData(true);
            $responseData['debug'] = $this->getDebugData($exception);
            $response->setData($responseData);
        }

        return $response;
    }

    /**
     * Laravel 12 兼容的 handle 方法（向后兼容）
     */
    public function handle(Throwable $exception): JsonResponse
    {
        return $this->handleApiException($exception, request());
    }
}
