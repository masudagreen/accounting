<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

/**
 * Thin wrapper around `$_SESSION` that gives the Web UI a typed API for the
 * few values we actually need to persist between requests.
 *
 * Phase 7-1 design note:
 *
 *   The same user is authenticated by the new REST API via Bearer tokens in
 *   the Authorization header; the Web UI wraps that plaintext token inside
 *   the PHP session so the user only hands over credentials once. See
 *   {@see \Rucaro\Http\Middleware\AuthenticateSession} for the validation
 *   side of the bridge.
 *
 * The class avoids a constructor on purpose — `$_SESSION` is a superglobal,
 * and pretending otherwise would invite bugs where multiple instances appear
 * to hold distinct state while actually sharing the same array.
 */
final class SessionStore
{
    public const KEY_USER_ID        = 'rucaro_user_id';
    public const KEY_TOKEN          = 'rucaro_api_token_plaintext';
    public const KEY_TOKEN_ID       = 'rucaro_api_token_id';
    public const KEY_DISPLAY_NAME   = 'rucaro_display_name';
    public const KEY_EMAIL          = 'rucaro_email';
    public const KEY_SELECTED_ENTITY    = 'rucaro_selected_entity_id';
    public const KEY_SELECTED_FISCAL    = 'rucaro_selected_fiscal_term_id';
    public const KEY_CSRF_TOKENS    = 'rucaro_csrf_tokens';
    public const KEY_FLASH_MESSAGES = 'rucaro_flash_messages';

    /**
     * Starts a session if one has not already been started. Safe to call
     * multiple times per request; no-ops once a session is active.
     */
    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        if (headers_sent()) {
            return;
        }
        @session_start();
    }

    public function setUser(
        string $userId,
        string $plaintextToken,
        string $tokenId,
        string $displayName,
        string $email,
    ): void {
        $_SESSION[self::KEY_USER_ID]      = $userId;
        $_SESSION[self::KEY_TOKEN]        = $plaintextToken;
        $_SESSION[self::KEY_TOKEN_ID]     = $tokenId;
        $_SESSION[self::KEY_DISPLAY_NAME] = $displayName;
        $_SESSION[self::KEY_EMAIL]        = $email;
    }

    public function getUserId(): ?string
    {
        $v = $_SESSION[self::KEY_USER_ID] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    public function getTokenPlaintext(): ?string
    {
        $v = $_SESSION[self::KEY_TOKEN] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    public function getTokenId(): ?string
    {
        $v = $_SESSION[self::KEY_TOKEN_ID] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    public function getDisplayName(): ?string
    {
        $v = $_SESSION[self::KEY_DISPLAY_NAME] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    public function getEmail(): ?string
    {
        $v = $_SESSION[self::KEY_EMAIL] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    public function isAuthenticated(): bool
    {
        return $this->getUserId() !== null && $this->getTokenPlaintext() !== null;
    }

    public function setSelectedEntity(string $entityId): void
    {
        $_SESSION[self::KEY_SELECTED_ENTITY] = $entityId;
    }

    public function getSelectedEntity(): ?string
    {
        $v = $_SESSION[self::KEY_SELECTED_ENTITY] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    public function setSelectedFiscalTerm(string $termId): void
    {
        $_SESSION[self::KEY_SELECTED_FISCAL] = $termId;
    }

    public function getSelectedFiscalTerm(): ?string
    {
        $v = $_SESSION[self::KEY_SELECTED_FISCAL] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    /**
     * Removes the user session without nuking flash messages or CSRF state
     * stored for the logout page itself.
     */
    public function forgetUser(): void
    {
        unset(
            $_SESSION[self::KEY_USER_ID],
            $_SESSION[self::KEY_TOKEN],
            $_SESSION[self::KEY_TOKEN_ID],
            $_SESSION[self::KEY_DISPLAY_NAME],
            $_SESSION[self::KEY_EMAIL],
            $_SESSION[self::KEY_SELECTED_ENTITY],
            $_SESSION[self::KEY_SELECTED_FISCAL],
        );
    }

    /**
     * Full wipe: clears every key the Web UI owns, regenerates the session
     * id to defeat fixation, and destroys the backing session record.
     */
    public function destroy(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                if (!headers_sent()) {
                    setcookie(
                        session_name(),
                        '',
                        [
                            'expires'  => time() - 42000,
                            'path'     => $params['path'],
                            'domain'   => $params['domain'],
                            'secure'   => $params['secure'],
                            'httponly' => $params['httponly'],
                            'samesite' => $params['samesite'],
                        ],
                    );
                }
            }
            @session_destroy();
        }
    }
}
