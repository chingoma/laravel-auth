<?php

namespace Lockminds\LaravelAuth\Helpers;

use Illuminate\Http\JsonResponse;

class Responses
{
    public static function success($code = 200, $message = 'success', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function error($code, $message = 'error', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function passwordResetLinkSuccess($code, $message = 'error', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function unhandledThrowable(\Throwable $throwable, $code, $message = 'unhandled exception', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => (getenv('APP_ENV') == 'local') ? $throwable->getMessage() : $message,
            'data' => $data,
        ]);
    }

    public static function unhandledException(\Exception $exception, $code, $message = 'unhandled exception', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => (getenv('APP_ENV') == 'local') ? $exception->getMessage() : $message,
            'data' => $data,
        ]);
    }

    public static function validationError($code, $message = 'validation error', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function unauthorized($code, $message = 'unauthorized', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function notFound($code, $message = 'not found', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function notAccepted($code, $message = 'not accepted', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function badCredentials($code, $message = 'bad credentials', $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
