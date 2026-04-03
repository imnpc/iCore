<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * API 统一响应 Trait。
 */
trait MakesApiResponses
{
    public static function success(mixed $data = null, string $message = '', int $code = 200, array $headers = []): JsonResponse
    {
        return self::respond(
            status: 'success',
            code: $code,
            message: $message,
            data: $data,
            errors: [],
            headers: $headers,
        );
    }

    public static function fail(string $message = '', int $code = 500, array $errors = [], array $headers = []): JsonResponse
    {
        return self::respond(
            status: 'fail',
            code: $code,
            message: $message,
            data: [],
            errors: $errors,
            headers: $headers,
        );
    }

    public static function error(string $message = '', int $code = 422, array $errors = [], array $headers = []): JsonResponse
    {
        return self::respond(
            status: 'error',
            code: $code,
            message: $message,
            data: [],
            errors: $errors,
            headers: $headers,
        );
    }

    private static function respond(
        string $status,
        int $code,
        string $message,
        mixed $data,
        array $errors,
        array $headers = [],
    ): JsonResponse {
        return response()->json([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $code, $headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
