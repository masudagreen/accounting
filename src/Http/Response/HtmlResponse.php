<?php

declare(strict_types=1);

namespace Rucaro\Http\Response;

/**
 * Minimal HTML / redirect response object used by the Web UI kernel.
 *
 * Kept separate from {@see JsonResponse} so that UI code does not accidentally
 * render HTML into the REST API surface. The constructor is exposed for tests;
 * controllers usually call one of the static helpers.
 */
final readonly class HtmlResponse
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public int $status,
        public array $headers,
        public string $body,
    ) {
    }

    public static function ok(string $html): self
    {
        return new self(
            200,
            ['Content-Type' => 'text/html; charset=utf-8'],
            $html,
        );
    }

    public static function of(int $status, string $html): self
    {
        return new self(
            $status,
            ['Content-Type' => 'text/html; charset=utf-8'],
            $html,
        );
    }

    public static function redirect(string $location, int $status = 303): self
    {
        return new self(
            $status,
            [
                'Location'      => $location,
                'Content-Type'  => 'text/html; charset=utf-8',
                'Cache-Control' => 'no-store',
            ],
            '',
        );
    }

    public static function notFound(string $message = 'Not Found'): self
    {
        $escaped = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return new self(
            404,
            ['Content-Type' => 'text/html; charset=utf-8'],
            '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1><p>' . $escaped . '</p>',
        );
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value, true);
            }
        }
        echo $this->body;
    }
}
