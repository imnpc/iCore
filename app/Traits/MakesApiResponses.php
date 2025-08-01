<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait MakesApiResponses
{
    public static function success($data = null, $message = '', $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => "success",
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
            'errors'  => [],
        ], $code);
    }

    public static function fail($message = '', $code = 500, $errors = []): JsonResponse
    {
        return response()->json([
            'status'  => "fail",
            'code'    => $code,
            'message' => $message,
            'data'    => [],
            'errors'  => $errors,
        ], $code);
    }

    public static function error($message = '', $code = 422, $errors = []): JsonResponse
    {
        return response()->json([
            'status'  => "error",
            'code'    => $code,
            'message' => $message,
            'data'    => [],
            'errors'  => $errors,
        ], $code);
    }
}
