<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

/**
 * One-shot flash message store. Messages queued on one request are delivered
 * to the template on the next request and then cleared.
 *
 * Kinds: `success`, `error`, `info`, `warning`. Unknown kinds are normalised
 * to `info` so templates can render safely without a dozen branches.
 */
final class FlashMessageBag
{
    public const KIND_SUCCESS = 'success';
    public const KIND_ERROR   = 'error';
    public const KIND_INFO    = 'info';
    public const KIND_WARNING = 'warning';

    public function addSuccess(string $message): void
    {
        $this->push(self::KIND_SUCCESS, $message);
    }

    public function addError(string $message): void
    {
        $this->push(self::KIND_ERROR, $message);
    }

    public function addInfo(string $message): void
    {
        $this->push(self::KIND_INFO, $message);
    }

    public function addWarning(string $message): void
    {
        $this->push(self::KIND_WARNING, $message);
    }

    /**
     * Drain every stored message. After this call the bag is empty.
     *
     * @return list<array{kind: string, message: string}>
     */
    public function consume(): array
    {
        $messages = $this->current();
        $_SESSION[SessionStore::KEY_FLASH_MESSAGES] = [];
        return $messages;
    }

    /**
     * Peek at the bag without draining it. Useful for tests.
     *
     * @return list<array{kind: string, message: string}>
     */
    public function current(): array
    {
        $raw = $_SESSION[SessionStore::KEY_FLASH_MESSAGES] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        /** @var list<array{kind: string, message: string}> $out */
        $out = [];
        foreach ($raw as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $kind = $entry['kind'] ?? null;
            $msg = $entry['message'] ?? null;
            if (is_string($kind) && is_string($msg) && $msg !== '') {
                $out[] = [
                    'kind'    => self::normaliseKind($kind),
                    'message' => $msg,
                ];
            }
        }
        return $out;
    }

    private function push(string $kind, string $message): void
    {
        $bag = $this->current();
        $bag[] = [
            'kind'    => self::normaliseKind($kind),
            'message' => $message,
        ];
        $_SESSION[SessionStore::KEY_FLASH_MESSAGES] = $bag;
    }

    private static function normaliseKind(string $kind): string
    {
        return match ($kind) {
            self::KIND_SUCCESS, self::KIND_ERROR, self::KIND_WARNING => $kind,
            default => self::KIND_INFO,
        };
    }
}
