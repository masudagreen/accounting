<?php

declare(strict_types=1);

namespace Rucaro\Http;

/**
 * Immutable snapshot of the currently-handled HTTP request.
 *
 * Stays intentionally small — Phase 3 does not need full PSR-7 semantics for
 * five endpoints. Later phases can swap this out without churn if / when we
 * adopt `guzzlehttp/psr7` as the canonical representation.
 *
 * @phpstan-type QueryMap array<string, string|int|bool|list<string>|null>
 */
final readonly class ServerRequest
{
    /**
     * @param array<string, string> $headers lowercased keys
     * @param QueryMap $query parsed query string
     * @param array<string, mixed>|list<mixed>|null $json parsed JSON body or null
     */
    public function __construct(
        public string $method,
        public string $path,
        public array $headers,
        public array $query,
        public ?array $json,
        public string $rawBody,
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $qmark = strpos($uri, '?');
        $path = $qmark === false ? $uri : substr($uri, 0, $qmark);

        /** @var array<string, string> $headers */
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (!is_string($v)) {
                continue;
            }
            if (str_starts_with($k, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($k, 5)));
                $headers[$name] = $v;
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }

        /** @var QueryMap $query */
        $query = [];
        foreach ($_GET as $k => $v) {
            if (!is_string($k)) {
                continue;
            }
            if (is_string($v)) {
                $query[$k] = $v;
            } else {
                $list = [];
                foreach ($v as $item) {
                    if (is_string($item)) {
                        $list[] = $item;
                    }
                }
                $query[$k] = $list;
            }
        }

        $raw = (string) file_get_contents('php://input');
        $json = null;
        if ($raw !== '') {
            try {
                $decoded = json_decode($raw, true, 64, \JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    /** @var array<string, mixed>|list<mixed> $decoded */
                    $json = $decoded;
                }
            } catch (\JsonException) {
                $json = null;
            }
        }

        return new self($method, $path, $headers, $query, $json, $raw);
    }

    public function header(string $name): ?string
    {
        $key = strtolower($name);

        return $this->headers[$key] ?? null;
    }

    /**
     * @return int<1, max>
     */
    public function positiveInt(string $name, int $default, int $min = 1, int $max = \PHP_INT_MAX): int
    {
        $raw = $this->query[$name] ?? null;
        if ($raw === null) {
            /** @var int<1, max> */
            return max($min, $default);
        }
        if (is_int($raw)) {
            $n = $raw;
        } elseif (is_string($raw) && ctype_digit($raw)) {
            $n = (int) $raw;
        } else {
            /** @var int<1, max> */
            return max($min, $default);
        }
        if ($n < $min) {
            $n = $min;
        }
        if ($n > $max) {
            $n = $max;
        }

        /** @var int<1, max> */
        return $n;
    }

    public function queryString(string $name): ?string
    {
        $v = $this->query[$name] ?? null;
        if (is_string($v) && $v !== '') {
            return $v;
        }

        return null;
    }

    public function queryBool(string $name): ?bool
    {
        $v = $this->query[$name] ?? null;
        if (is_bool($v)) {
            return $v;
        }
        if (is_string($v)) {
            $lc = strtolower($v);
            if ($lc === 'true' || $lc === '1') {
                return true;
            }
            if ($lc === 'false' || $lc === '0') {
                return false;
            }
        }

        return null;
    }
}
