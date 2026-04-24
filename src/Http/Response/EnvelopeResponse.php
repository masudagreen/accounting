<?php

declare(strict_types=1);

namespace Rucaro\Http\Response;

/**
 * Helper that builds the `{success, data, error, meta}` envelope used by every
 * Rucaro API response.
 *
 * Returns a {@see JsonResponse} so the same `->send()` path already in
 * `public/api/v1/index.php` renders both success and error bodies.
 */
final class EnvelopeResponse
{
    /**
     * @param array<string, mixed>|list<mixed>|null $data
     * @param array<string, int>|null $meta
     * @param array<string, string>   $extraHeaders
     */
    public static function ok(
        array|null $data = null,
        ?array $meta = null,
        int $status = 200,
        array $extraHeaders = [],
    ): JsonResponse {
        $payload = [
            'success' => true,
            'data'    => $data,
            'error'   => null,
            'meta'    => $meta,
        ];
        return JsonResponse::of($status, $payload, $extraHeaders);
    }

    /**
     * @param array<string, int> $meta
     * @param list<mixed>        $items
     */
    public static function list(
        array $items,
        array $meta,
        int $status = 200,
    ): JsonResponse {
        $payload = [
            'success' => true,
            'data'    => $items,
            'error'   => null,
            'meta'    => $meta,
        ];
        return JsonResponse::of($status, $payload);
    }
}
