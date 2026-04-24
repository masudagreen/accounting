<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Crypto;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Crypto\AesGcmCipher;
use Rucaro\Infrastructure\Crypto\Exception\CryptoException;
use Rucaro\Infrastructure\Crypto\LegacyBlowfishDecryptor;
use Rucaro\Infrastructure\Crypto\VersionedCipher;

#[CoversClass(VersionedCipher::class)]
#[CoversClass(CryptoException::class)]
final class VersionedCipherTest extends TestCase
{
    private const MASTER_KEY_B64 = 'AAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8';
    private const LEGACY_SECRET  = 'legacy-master-secret-xyz';

    private AesGcmCipher $aesGcmCipher;

    protected function setUp(): void
    {
        $this->aesGcmCipher = new AesGcmCipher(self::MASTER_KEY_B64);
    }

    public function testEncryptAlwaysDelegatesToAesGcm(): void
    {
        $cipher = new VersionedCipher($this->aesGcmCipher);

        $token = $cipher->encrypt('hello', 'ctx');

        self::assertStringStartsWith('v2:k1:', $token);
        self::assertSame('hello', $cipher->decrypt($token, 'ctx'));
    }

    public function testDecryptRoutesV2TokensToAesGcm(): void
    {
        $cipher = new VersionedCipher($this->aesGcmCipher);

        // Produce a token directly via the AES backend, then hand it to the
        // facade. If dispatch is wrong, this will throw.
        $directToken = $this->aesGcmCipher->encrypt('payload', 'aad');

        self::assertSame('payload', $cipher->decrypt($directToken, 'aad'));
    }

    public function testDecryptWithoutV2PrefixRoutesToLegacy(): void
    {
        if (!LegacyBlowfishDecryptor::isAvailable()) {
            self::markTestSkipped(
                'Legacy provider unavailable; routing to Blowfish cannot be exercised end-to-end.',
            );
        }

        $legacy = new LegacyBlowfishDecryptor(self::LEGACY_SECRET);
        $cipher = new VersionedCipher($this->aesGcmCipher, $legacy);

        // Build a legacy blob using the same reference recipe documented in
        // encrypted-columns.md §4. Padded to a block boundary.
        $plaintext  = 'legacy-pw';
        $padded     = $plaintext . str_repeat("\0", 8 - (strlen($plaintext) % 8));
        $key        = substr(md5(self::LEGACY_SECRET), 0, 56);
        $iv         = substr(md5($key), 0, 8);
        $ciphertext = openssl_encrypt(
            $padded,
            'bf-cbc',
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv,
        );
        self::assertNotFalse($ciphertext);

        self::assertSame($plaintext, $cipher->decrypt($ciphertext));
    }

    public function testDecryptLegacyWithoutConfiguredDecryptorThrows(): void
    {
        $cipher = new VersionedCipher($this->aesGcmCipher); // no legacy backend

        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('no LegacyBlowfishDecryptor is configured');

        $cipher->decrypt("\x00\x01\x02\x03\x04\x05\x06\x07");
    }

    public function testDecryptV2TokenNeverFallsThroughToLegacy(): void
    {
        // If dispatch accidentally routed v2 tokens to the legacy decryptor,
        // the resulting CryptoException would carry a "Legacy Blowfish"
        // prefix (or, when the legacy provider isn't loaded, a provider
        // error). We assert the AES-GCM branch message surfaces instead,
        // which can only happen if dispatch chose the v2 path.
        $legacy = new LegacyBlowfishDecryptor('unused-in-this-test');
        $cipher = new VersionedCipher($this->aesGcmCipher, $legacy);

        $token    = $this->aesGcmCipher->encrypt('payload', 'ctx');
        $tampered = self::flipTagBit($token);

        try {
            $cipher->decrypt($tampered, 'ctx');
            self::fail('Tampered v2 token must not decrypt.');
        } catch (CryptoException $e) {
            self::assertStringContainsString('AES-GCM', $e->getMessage());
            self::assertStringNotContainsString('Legacy', $e->getMessage());
        }
    }

    private static function flipTagBit(string $token): string
    {
        $parts = explode(':', $token, 3);
        self::assertCount(3, $parts);

        $payload = $parts[2];
        $pad     = strlen($payload) % 4;
        if ($pad !== 0) {
            $payload .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode(strtr($payload, '-_', '+/'), true);
        self::assertIsString($decoded);

        $idx           = strlen($decoded) - 1;
        $decoded[$idx] = chr(ord($decoded[$idx]) ^ 0x01);

        $parts[2] = rtrim(strtr(base64_encode($decoded), '+/', '-_'), '=');

        return implode(':', $parts);
    }
}
