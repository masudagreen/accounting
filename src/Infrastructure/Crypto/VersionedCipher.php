<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

use Rucaro\Infrastructure\Crypto\Exception\CryptoException;

/**
 * Dispatching facade over {@see AesGcmCipher} (new v2 tokens) and
 * {@see LegacyBlowfishDecryptor} (old raw Blowfish blobs). See ADR-003 §3.4.
 *
 * Dispatch rules:
 *   - encrypt(): always delegates to AesGcmCipher (new writes never use legacy).
 *   - decrypt(): if the input starts with the current v2 schema prefix
 *     ("v2:"), delegate to AesGcmCipher; otherwise fall through to the
 *     LegacyBlowfishDecryptor when one is configured. Missing legacy
 *     decryptor + non-v2 input -> CryptoException.
 *
 * The legacy decryptor is optional: production runs that never touch the old
 * database should leave it null and get a clear error on accidental legacy
 * input.
 */
final readonly class VersionedCipher implements CipherInterface
{
    private const V2_PREFIX = 'v2:';

    public function __construct(
        private AesGcmCipher $aesGcmCipher,
        private ?LegacyBlowfishDecryptor $legacyDecryptor = null,
    ) {
    }

    public function encrypt(string $plaintext, string $aad = ''): string
    {
        return $this->aesGcmCipher->encrypt($plaintext, $aad);
    }

    public function decrypt(string $ciphertext, string $aad = ''): string
    {
        if (str_starts_with($ciphertext, self::V2_PREFIX)) {
            return $this->aesGcmCipher->decrypt($ciphertext, $aad);
        }

        if ($this->legacyDecryptor === null) {
            throw new CryptoException(
                'Legacy ciphertext received but no LegacyBlowfishDecryptor is configured.',
            );
        }

        return $this->legacyDecryptor->decrypt($ciphertext, $aad);
    }
}
