<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

use Rucaro\Infrastructure\Crypto\Exception\CryptoException;

/**
 * Byte-exact decryption for legacy Blowfish-CBC ciphertexts produced by the
 * pre-PHP 8 Rucaro codebase (docs/phase1/encrypted-columns.md §3-4).
 *
 * Algorithm (bug-for-bug compatible with old mcrypt output):
 *   - Cipher: bf-cbc
 *   - Key   : substr(md5($masterSecret), 0, 56)
 *   - IV    : substr(md5($key), 0, 8)        // deterministic, NOT secure
 *   - Padding: zero-padding (stripped via rtrim $"\0")
 *
 * This class deliberately implements decrypt() only. Calling encrypt() is a
 * programming error: new writes MUST go through AesGcmCipher. See ADR-003 §2.5.
 *
 * Note: OpenSSL 3.x disables Blowfish by default. The runtime must enable the
 * "legacy" provider (e.g. via OPENSSL_CONF) for bf-cbc to be available.
 * Callers that must survive on stock OpenSSL 3 builds should guard with
 * {@see self::isAvailable()}.
 */
final readonly class LegacyBlowfishDecryptor
{
    private const CIPHER       = 'bf-cbc';
    private const KEY_BYTES    = 56;
    private const IV_BYTES     = 8;

    public function __construct(private string $legacyMasterSecret)
    {
        if ($this->legacyMasterSecret === '') {
            throw new CryptoException('Legacy master secret must not be empty.');
        }
    }

    /**
     * Returns true when the current OpenSSL build exposes the bf-cbc cipher.
     * Useful for integration tests that must skip cleanly on hardened
     * OpenSSL 3 builds without the legacy provider.
     */
    public static function isAvailable(): bool
    {
        return in_array(self::CIPHER, openssl_get_cipher_methods(true), true);
    }

    /**
     * Not supported. Re-encrypting under the legacy scheme is a footgun.
     *
     * @throws CryptoException Always.
     */
    public function encrypt(string $plaintext, string $aad = ''): string
    {
        throw new CryptoException(
            'LegacyBlowfishDecryptor is read-only; use AesGcmCipher for new writes.',
        );
    }

    /**
     * Decrypt a raw Blowfish-CBC blob from the legacy database.
     *
     * @param string $ciphertext Raw binary blob as stored in the legacy BLOB column.
     * @param string $aad        Ignored; the legacy scheme did not use AAD.
     *                           Accepted for interface symmetry with {@see CipherInterface}.
     *
     * @throws CryptoException On any OpenSSL failure (incl. missing legacy provider).
     */
    public function decrypt(string $ciphertext, string $aad = ''): string
    {
        unset($aad); // legacy format is not AEAD; AAD is ignored by design.

        $key = substr(md5($this->legacyMasterSecret), 0, self::KEY_BYTES);
        $iv  = substr(md5($key), 0, self::IV_BYTES);

        $plain = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv,
        );
        if ($plain === false) {
            throw new CryptoException(
                'Legacy Blowfish decryption failed: '
                . (openssl_error_string() ?: 'unknown (is the OpenSSL legacy provider enabled?)'),
            );
        }

        return rtrim($plain, "\0");
    }
}
