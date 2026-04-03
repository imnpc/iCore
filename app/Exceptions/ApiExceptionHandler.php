<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\MakesApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

/**
 * Laravel 13 API 异常处理器。
 */
class ApiExceptionHandler extends ExceptionHandler
{
    use MakesApiResponses;

    /**
     * @var array<class-string<Throwable>, array{message: string, status: int, level: string, hide_in_production?: bool}>
     */
    private const EXCEPTION_CONFIG = [
        ValidationException::class => [
            'message' => '请求参数验证失败',
            'status' => 422,
            'level' => 'warning',
        ],
        AuthenticationException::class => [
            'message' => '认证失败，请重新登录',
            'status' => 401,
            'level' => 'warning',
        ],
        AuthorizationException::class => [
            'message' => '权限不足，拒绝访问',
            'status' => 403,
            'level' => 'warning',
        ],
        ModelNotFoundException::class => [
            'message' => '请求的资源不存在',
            'status' => 404,
            'level' => 'notice',
        ],
        NotFoundHttpException::class => [
            'message' => '请求的路由不存在',
            'status' => 404,
            'level' => 'notice',
        ],
        MethodNotAllowedHttpException::class => [
            'message' => '请求方法不允许',
            'status' => 405,
            'level' => 'warning',
        ],
        TooManyRequestsHttpException::class => [
            'message' => '请求过于频繁，请稍后再试',
            'status' => 429,
            'level' => 'warning',
        ],
        ThrottleRequestsException::class => [
            'message' => '请求过于频繁，请稍后再试',
            'status' => 429,
            'level' => 'warning',
        ],
        UnprocessableEntityHttpException::class => [
            'message' => '无法处理的请求实体',
            'status' => 422,
            'level' => 'warning',
        ],
        QueryException::class => [
            'message' => '数据库查询错误',
            'status' => 500,
            'level' => 'error',
            'hide_in_production' => true,
        ],
    ];

    /**
     * @var array<int, string>
     */
    private const HTTP_STATUS_MESSAGES = [
        400 => '请求参数错误',
        401 => '未授权访问',
        403 => '禁止访问',
        404 => '资源不存在',
        405 => '请求方法不允许',
        409 => '资源冲突',
        422 => '请求参数验证失败',
        429 => '请求过于频繁',
        500 => '服务器内部错误',
        502 => '网关错误',
        503 => '服务不可用',
    ];

    /**
     * @var array<int, class-string<Throwable>>
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

    public function render($request, Throwable $e): Response
    {
        if (! $request instanceof Request) {
            return parent::render($request, $e);
        }

        if ($this->isApiRequest($request)) {
            return $this->handleApiException($e, $request);
        }

        return parent::render($request, $e);
    }

    public function handleApiException(Throwable $exception, ?Request $request = null): JsonResponse
    {
        $request ??= request();
        $traceId = $this->resolveTraceId($request);

        $this->logException($exception, $request, $traceId);

        if ($exception instanceof HttpResponseException) {
            return $this->handleHttpResponseException($exception, $traceId);
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception, $traceId);
        }

        if ($exception instanceof HttpException) {
            return $this->handleHttpException($exception, $traceId);
        }

        $config = $this->resolveExceptionConfig($exception);
        if ($config !== null) {
            return $this->handleKnownException($exception, $config, $traceId);
        }

        return $this->handleUnknownException($exception, $traceId);
    }

    public function handle(Throwable $exception): JsonResponse
    {
        return $this->handleApiException($exception, request());
    }

    private function handleValidationException(ValidationException $exception, string $traceId): JsonResponse
    {
        $config = self::EXCEPTION_CONFIG[ValidationException::class];
        $message = $exception->getMessage() !== '' ? $exception->getMessage() : $config['message'];

        $response = $this->error(
            message: $message,
            code: $config['status'],
            errors: $exception->errors(),
        );

        return $this->finalizeResponse($response, $exception, $traceId);
    }

    private function handleHttpException(HttpException $exception, string $traceId): JsonResponse
    {
        $statusCode = $exception->getStatusCode();
        $message = $exception->getMessage();

        if ($message === '') {
            $message = self::HTTP_STATUS_MESSAGES[$statusCode] ?? '请求处理失败';
        }

        if ($this->isProduction() && $statusCode >= 500) {
            $message = self::HTTP_STATUS_MESSAGES[500];
        }

        $response = $this->error(
            message: $message,
            code: $statusCode,
            errors: [],
            headers: $exception->getHeaders(),
        );

        return $this->finalizeResponse($response, $exception, $traceId);
    }

    private function handleHttpResponseException(HttpResponseException $exception, string $traceId): JsonResponse
    {
        $response = $exception->getResponse();

        if ($response instanceof JsonResponse) {
            return $this->finalizeResponse($response, $exception, $traceId);
        }

        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 500;
        $errors = [];

        if (! $this->isProduction() && method_exists($response, 'getContent')) {
            $content = (string) $response->getContent();
            $errors['message'] = mb_substr(strip_tags($content), 0, 3000);
        }

        $json = $this->error('请求处理失败', $statusCode, $errors);

        return $this->finalizeResponse($json, $exception, $traceId);
    }

    /**
     * @param  array{message: string, status: int, level: string, hide_in_production?: bool}  $config
     */
    private function handleKnownException(Throwable $exception, array $config, string $traceId): JsonResponse
    {
        $message = $config['message'];

        if ($this->isProduction() && isset($config['hide_in_production'])) {
            $message = '服务器处理请求时出现错误';
        }

        $response = $this->error($message, $config['status'], []);

        return $this->finalizeResponse($response, $exception, $traceId);
    }

    private function handleUnknownException(Throwable $exception, string $traceId): JsonResponse
    {
        $message = $this->isProduction() ? self::HTTP_STATUS_MESSAGES[500] : $exception->getMessage();
        $response = $this->error($message, 500, []);

        return $this->finalizeResponse($response, $exception, $traceId);
    }

    private function isApiRequest(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson() || $request->wantsJson();
    }

    private function isProduction(): bool
    {
        return app()->isProduction();
    }

    private function finalizeResponse(JsonResponse $response, Throwable $exception, string $traceId): JsonResponse
    {
        $response->headers->set('X-Trace-Id', $traceId);

        if (! $this->isProduction()) {
            $responseData = $response->getData(true);
            $responseData['debug'] = $this->getDebugData($exception, $traceId);
            $response->setData($responseData);
        }

        return $response;
    }

    private function resolveTraceId(Request $request): string
    {
        $traceId = trim((string) $request->header('X-Trace-ID', ''));

        if ($traceId !== '') {
            return $traceId;
        }

        return (string) Str::uuid();
    }

    private function logException(Throwable $exception, Request $request, string $traceId): void
    {
        $config = $this->resolveExceptionConfig($exception);
        $level = $config['level'] ?? ($this->shouldReport($exception) ? 'error' : 'warning');

        $context = [
            'trace_id' => $traceId,
            'exception_class' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'request' => [
                'method' => $request->getMethod(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $request->user()?->id,
            ],
        ];

        if ($this->shouldReport($exception)) {
            $context['exception'] = $exception;
        }

        Log::log($level, 'API异常: '.($exception->getMessage() ?: class_basename($exception)), $context);
    }

    /**
     * @return array{message: string, status: int, level: string, hide_in_production?: bool}|null
     */
    private function resolveExceptionConfig(Throwable $exception): ?array
    {
        foreach (self::EXCEPTION_CONFIG as $class => $config) {
            if ($exception instanceof $class) {
                return $config;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function getDebugData(Throwable $exception, string $traceId): array
    {
        return [
            'trace_id' => $traceId,
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'previous' => $exception->getPrevious()?->getMessage(),
        ];
    }
}
