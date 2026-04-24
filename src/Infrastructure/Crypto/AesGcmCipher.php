<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

use Rucaro\Infrastructure\Crypto\Exception\CryptoException;

/**
 * AES-256-GCM cipher with HKDF-SHA256 key derivation.
 *
 * Token format (ADR-003 §2.4):
 *     v2:k1:<base64url(nonce(12) || ciphertext(N) || tag(16))>
 *
 * Key material:
 *   - Constructor receives the master key as base64url (same format stored in
 *     the APP_ENCRYPTION_KEY environment variable). It MUST decode to exactly
 *     32 bytes; anything else raises CryptoException at construction time so
 *     configuration errors fail fast.
 *   - Per-operation key is derived via hash_hkdf('sha256', masterKey, 32, info)
 *     where info binds the application, key version, and AAD context. Using a
 *     different AAD therefore yields a different derived key AND a different
 *     authentication tag, providing defence in depth against context confusion.
 */
final readonly class AesGcmCipher implements CipherInterface
{
    private const CIPHER       = 'aes-256-gcm';
    private const NONCE_BYTES  = 12;
    private const TAG_BYTES    = 16;
    private const SCHEMA       = 'v2';
    private const KEY_BYTES    = 32;

    /**
     * Raw 32-byte master key decoded from the constructor argument.
     */
    private string $masterKey;

    /**
     * @param string $masterKeyBase64 Base64url-encoded 32-byte master key.
     * @param string $keyId           Key version identifier (e.g. "k1").
     *                                Used as the second token segment and
     *                                mixed into the HKDF info string.
     *
     * @throws CryptoException If the key does not decode to exactly 32 bytes
     *                         or the keyId is empty/contains ':' (reserved).
     */
    public function __construct(
        string $masterKeyBase64,
        private string $keyId = 'k1',
    ) {
        if ($this->keyId === '' || str_contains($this->keyId, ':')) {
            throw new CryptoException('keyId must be non-empty and must not contain ":".');
        }

        $raw = self::base64UrlDecode($masterKeyBase64);
        if (strlen($raw) !== self::KEY_BYTES) {
            throw new CryptoException(sprintf(
                'APP_ENCRYPTION_KEY must decode to exactly %d bytes, got %d.',
                self::KEY_BYTES,
                strlen($raw),
            ));
        }
        $this->masterKey = $raw;
    }

    public function encrypt(string $plaintext, string $aad = ''): string
    {
        $nonce   = random_bytes(self::NONCE_BYTES);
        $tag     = '';
        $derived = $this->deriveKey($aad);

        $cipher = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $derived,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad,
            self::TAG_BYTES,
        );
        if ($cipher === false) {
            throw new CryptoException(
                'AES-GCM encryption failed: ' . (openssl_error_string() ?: 'unknown error'),
            );
        }

        $payload = $nonce . $cipher . $tag;

        return sprintf('%s:%s:%s', self::SCHEMA, $this->keyId, self::base64UrlEncode($payload));
    }

    public function decrypt(string $ciphertext, string $aad = ''): string
    {
        $parts = explode(':', $ciphertext, 3);
        if (count($parts) !== 3 || $parts[0] !== self::SCHEMA || $parts[1] !== $this->keyId) {
            throw new CryptoException(
                'Unsupported cipher token format: ' . substr($ciphertext, 0, 16),
            );
        }

        $blob = self::base64UrlDecode($parts[2]);
        // Minimum valid payload = nonce + tag (empty plaintext is legal).
        if (strlen($blob) < self::NONCE_BYTES + self::TAG_BYTES) {
            throw new CryptoException('Cipher payload too short.');
        }

        $nonce      = substr($blob, 0, self::NONCE_BYTES);
        $tag        = substr($blob, -self::TAG_BYTES);
        $cipherBody = substr($blob, self::NONCE_BYTES, -self::TAG_BYTES);

        $plain = openssl_decrypt(
            $cipherBody,
            self::CIPHER,
            $this->deriveKey($aad),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad,
        );
        if ($plain === false) {
            throw new CryptoException(
                'AES-GCM decryption failed (tamper / wrong key / wrong AAD).',
            );
        }

        return $plain;
    }

    /**
     * Derive a 32-byte sub-key bound to the key version and AAD context.
     *
     * Using the AAD inside the HKDF info means:
     *   - Same master key + different AAD -> different derived key,
     *     a belt-and-braces defence on top of GCM's own AAD check.
     *   - Keeps master key compromise blast radius contained per column.
     */
    private function deriveKey(string $aad): string
    {
        $info = sprintf('rucaro:%s:%s', $this->keyId, $aad !== '' ? $aad : 'default');

        return hash_hkdf('sha256', $this->masterKey, self::KEY_BYTES, $info, '');
    }

    private static function base64UrlEncode(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $encoded): string
    {
        $pad = strlen($encoded) % 4;
        if ($pad !== 0) {
            $encoded .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);
        if ($decoded === false) {
            throw new CryptoException('Invalid base64url payload.');
        }

        return $decoded;
    }
}
