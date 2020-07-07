<?php


namespace App\Classes;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

class JsonReturnFormat
{
    /**
     * Compose JSON return data for exception
     * @param int $code
     * @param string $message
     * @return JsonResponse
     */
    public static function exception(int $code, string $message): JsonResponse
    {
        return response()->json([
            'status_code' => $code,
            'error' => $message,
            'status' => 'error',
        ], $code);
    }

    /**
     * Compose JSON return data for validation errors
     * @param MessageBag $errors
     * @return JsonResponse
     */
    public static function validation(MessageBag $errors): JsonResponse
    {
        return response()->json([
            'status_code' => 422,
            'status' => 'error',
            'errors' => $errors->toArray(),
        ], 422);
    }
}
