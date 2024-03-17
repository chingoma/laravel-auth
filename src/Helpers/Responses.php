<?php

namespace Lockminds\LaravelAuth\Helpers;

use Illuminate\Http\JsonResponse;

class Responses
{
    public static function success($message = 'Successfully.', $data = [], $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function error($message = 'An error occurred.', $data = [],$code = 400 ): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function passwordResetLinkSuccess($message = 'Password resent link sent successfully.', $data = [],$code = 200): JsonResponse
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
        report($throwable);

        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => (getenv('APP_ENV') == 'local') ? $throwable->getMessage() : $message,
            'data' => $data,
        ]);
    }

    public static function unhandledException(\Exception $exception, $code, $message = 'unhandled exception', $data = []): JsonResponse
    {
        report($exception);

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

    public static function badCredentials($message = 'bad credentials', $data = [], $code = 400): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
