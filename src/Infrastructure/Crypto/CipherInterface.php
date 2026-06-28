<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

use Rucaro\Infrastructure\Crypto\Exception\CryptoException;

/**
 * Symmetric cipher contract (see ADR-003 §3.1).
 *
 * Implementations MUST:
 *   - Accept binary-safe plaintext / ciphertext.
 *   - Bind the AAD (Additional Authenticated Data) into the authentication
 *     tag. Using a different AAD on decrypt MUST fail with CryptoException.
 *   - Produce self-describing tokens that can be dispatched by
 *     VersionedCipher based on a textual prefix (e.g. "v2:k1:").
 */
interface CipherInterface
{
    /**
     * Encrypt $plaintext and return a self-contained ciphertext token.
     *
     * @param string $plaintext binary-safe plaintext
     * @param string $aad Additional Authenticated Data. Recommended
     *                    format: "{table}/{column}/{primaryKey}".
     *
     * @return string Encrypted token (e.g. "v2:k1:<base64url>").
     *
     * @throws CryptoException when the underlying primitive fails
     */
    public function encrypt(string $plaintext, string $aad = ''): string;

    /**
     * Decrypt a token produced by {@see encrypt()} (or a legacy variant when
     * wrapped by VersionedCipher) and return the original plaintext.
     *
     * @param string $ciphertext token as returned by encrypt() or legacy blob
     * @param string $aad must match the AAD passed to encrypt()
     *
     * @return string original plaintext, byte-for-byte
     *
     * @throws CryptoException on tampering, wrong key, wrong AAD, or malformed input
     */
    public function decrypt(string $ciphertext, string $aad = ''): string;
}
