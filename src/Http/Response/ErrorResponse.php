<?php

declare(strict_types=1);

namespace Rucaro\Http\Response;

/**
 * Helper for the failure envelope. The body always has `success=false`,
 * `data=null`, and a non-null `error` block; `meta` stays optional.
 */
final class ErrorResponse
{
    /**
     * @param array<string, mixed>|null $details
     * @param array<string, string>     $extraHeaders
     */
    public static function of(
        int $status,
        string $code,
        string $message,
        ?array $details = null,
        array $extraHeaders = [],
    ): JsonResponse {
        $error = [
            'code'    => $code,
            'message' => $message,
        ];
        if ($details !== null) {
            $error['details'] = $details;
        }
        $payload = [
            'success' => false,
            'data'    => null,
            'error'   => $error,
            'meta'    => null,
        ];
        return JsonResponse::of($status, $payload, $extraHeaders);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::of(401, 'UNAUTHORIZED', $message);
    }

    public static function badRequest(string $message = 'Bad request'): JsonResponse
    {
        return self::of(400, 'BAD_REQUEST', $message);
    }

    public static function notFound(string $message = 'Not found'): JsonResponse
    {
        return self::of(404, 'NOT_FOUND', $message);
    }

    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::of(403, 'FORBIDDEN', $message);
    }

    /**
     * @param array<string, list<string>> $fieldErrors
     */
    public static function unprocessable(
        string $message,
        array $fieldErrors = [],
        string $code = 'VALIDATION_FAILED',
    ): JsonResponse {
        return self::of(422, $code, $message, ['errors' => $fieldErrors]);
    }
}
