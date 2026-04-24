<?php

declare(strict_types=1);

namespace Rucaro\Http\Response;

/**
 * Tiny helper for emitting a JSON HTTP response.
 *
 * Deliberately minimal: no PSR-7 yet. Phase 3 will replace this with a
 * proper Response abstraction once FastRoute + the HTTP stack solidifies.
 */
final readonly class JsonResponse
{
    public function __construct(
        public int $status,
        /** @var array<string, string> */
        public array $headers,
        public string $body,
    ) {
    }

    /**
     * @param array<string, mixed>|list<mixed> $payload
     * @param array<string, string> $extraHeaders
     */
    public static function of(int $status, array $payload, array $extraHeaders = []): self
    {
        $body = json_encode(
            $payload,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );
        $headers = array_merge(
            ['Content-Type' => 'application/json; charset=utf-8'],
            $extraHeaders,
        );
        return new self($status, $headers, $body);
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
