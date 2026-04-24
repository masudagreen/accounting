<?php

declare(strict_types=1);

namespace Rucaro\Http;

use Rucaro\Http\Response\JsonResponse;

/**
 * Minimal front-controller kernel for the new application surface.
 *
 * Phase 1.3 scope: reply with a short JSON greeting for any request so we
 * can wire up Apache -> public/index.php -> Kernel end-to-end and verify
 * autoloading and routing plumbing. Phase 3 replaces this with a real
 * PSR-15 middleware pipeline and FastRoute dispatcher.
 */
final class Kernel
{
    public function handle(): JsonResponse
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->pathOf($_SERVER['REQUEST_URI'] ?? '/');

        if ($method === 'GET' && ($path === '/' || $path === '')) {
            return JsonResponse::of(200, [
                'success' => true,
                'data'    => [
                    'name'    => 'Rucaro v2',
                    'message' => 'Rucaro Accounting v2 front controller is alive.',
                ],
                'error'   => null,
            ]);
        }

        return JsonResponse::of(404, [
            'success' => false,
            'data'    => null,
            'error'   => ['code' => 'not_found', 'message' => 'Not Found'],
        ]);
    }

    private function pathOf(string $uri): string
    {
        $qmark = strpos($uri, '?');
        return $qmark === false ? $uri : substr($uri, 0, $qmark);
    }
}
