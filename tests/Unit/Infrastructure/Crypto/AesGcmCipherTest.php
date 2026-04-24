<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Crypto;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Crypto\AesGcmCipher;
use Rucaro\Infrastructure\Crypto\Exception\CryptoException;

#[CoversClass(AesGcmCipher::class)]
#[CoversClass(CryptoException::class)]
final class AesGcmCipherTest extends TestCase
{
    /**
     * Deterministic 32-byte key encoded as base64url (no padding). Used so
     * test output is reproducible across runs. NOT a real secret.
     */
    private const MASTER_KEY_B64 = 'AAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8';

    private AesGcmCipher $cipher;

    protected function setUp(): void
    {
        $this->cipher = new AesGcmCipher(self::MASTER_KEY_B64);
    }

    public function testEncryptDecryptRoundTripPreservesPlaintext(): void
    {
        $plaintext = 'hello world';
        $aad       = 'accountingFile/strPassword/42';

        $token = $this->cipher->encrypt($plaintext, $aad);

        self::assertSame($plaintext, $this->cipher->decrypt($token, $aad));
    }

    public function testEncryptProducesV2k1Prefix(): void
    {
        $token = $this->cipher->encrypt('payload', 'ctx');

        self::assertStringStartsWith('v2:k1:', $token);
    }

    public function testCustomKeyIdIsReflectedInTokenPrefix(): void
    {
        $cipher = new AesGcmCipher(self::MASTER_KEY_B64, 'k2');

        $token = $cipher->encrypt('payload', 'ctx');

        self::assertStringStartsWith('v2:k2:', $token);
        self::assertSame('payload', $cipher->decrypt($token, 'ctx'));
    }

    public function testEncryptingSamePlaintextTwiceYieldsDifferentCiphertexts(): void
    {
        $plaintext = 'same-input';
        $aad       = 'accountingFile/strPassword/1';

        $a = $this->cipher->encrypt($plaintext, $aad);
        $b = $this->cipher->encrypt($plaintext, $aad);

        self::assertNotSame($a, $b, 'Random nonce must yield distinct ciphertexts.');
        self::assertSame($plaintext, $this->cipher->decrypt($a, $aad));
        self::assertSame($plaintext, $this->cipher->decrypt($b, $aad));
    }

    public function testDecryptFailsWhenAadDoesNotMatch(): void
    {
        $token = $this->cipher->encrypt('secret', 'ctx-a');

        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('AES-GCM decryption failed');

        $this->cipher->decrypt($token, 'ctx-b');
    }

    public function testDecryptFailsWhenTagIsTampered(): void
    {
        $aad   = 'accountingLogBanksAccount/blobDetail/7';
        $token = $this->cipher->encrypt('confidential', $aad);

        $tampered = self::flipLastBase64Byte($token);
        self::assertNotSame($token, $tampered, 'precondition: tampered token must differ');

        $this->expectException(CryptoException::class);

        $this->cipher->decrypt($tampered, $aad);
    }

    public function testDecryptFailsOnUnknownSchemaVersion(): void
    {
        $token   = $this->cipher->encrypt('payload', 'ctx');
        // Replace the "v2" segment with "v9".
        $swapped = 'v9' . substr($token, 2);

        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('Unsupported cipher token format');

        $this->cipher->decrypt($swapped, 'ctx');
    }

    public function testDecryptFailsOnUnknownKeyVersion(): void
    {
        $token   = $this->cipher->encrypt('payload', 'ctx');
        $swapped = preg_replace('/^v2:k1:/', 'v2:k9:', $token, 1);
        self::assertIsString($swapped);

        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('Unsupported cipher token format');

        $this->cipher->decrypt($swapped, 'ctx');
    }

    public function testDecryptFailsOnShortPayload(): void
    {
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('Cipher payload too short');

        // base64url("abc") -> "YWJj" — well under NONCE(12)+TAG(16)+1 bytes.
        $this->cipher->decrypt('v2:k1:YWJj', 'ctx');
    }

    public function testDecryptFailsOnInvalidBase64Payload(): void
    {
        $this->expectException(CryptoException::class);

        // '!' is not in the base64url alphabet.
        $this->cipher->decrypt('v2:k1:!!!', 'ctx');
    }

    public function testConstructorRejectsKeyOfWrongLength(): void
    {
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('must decode to exactly 32 bytes');

        new AesGcmCipher('dG9vLXNob3J0'); // "too-short" -> 9 bytes.
    }

    public function testConstructorRejectsEmptyKeyId(): void
    {
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('keyId');

        new AesGcmCipher(self::MASTER_KEY_B64, '');
    }

    public function testConstructorRejectsKeyIdContainingColon(): void
    {
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('keyId');

        new AesGcmCipher(self::MASTER_KEY_B64, 'k:1');
    }

    public function testConstructorRejectsInvalidBase64(): void
    {
        $this->expectException(CryptoException::class);

        new AesGcmCipher('!!!not-base64!!!');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function plaintextVariantsProvider(): iterable
    {
        yield 'empty'          => [''];
        yield 'short ascii'    => ['p'];
        yield 'japanese utf8'  => ['日本語パスワード'];
        yield 'json blob'      => ['{"user":"山田","pin":"1234","notes":"テスト"}'];
        yield 'binary bytes'   => ["\x00\x01\x02\xFF\xFEtrailing\x00"];
        yield 'long'           => [str_repeat('A', 4096)];
    }

    #[DataProvider('plaintextVariantsProvider')]
    public function testRoundTripAcrossPlaintextVariants(string $plaintext): void
    {
        $aad = 'accountingBlueSheetJpn/blobData/999';

        $token  = $this->cipher->encrypt($plaintext, $aad);
        $result = $this->cipher->decrypt($token, $aad);

        self::assertSame($plaintext, $result);
    }

    /**
     * Flip one bit inside the GCM tag (last 16 bytes of the decoded blob)
     * and re-encode. Operating on the decoded bytes, not on the base64
     * surface, makes the test deterministic regardless of padding alignment.
     */
    private static function flipLastBase64Byte(string $token): string
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

        // Flip the low bit of the final byte (inside the 16-byte tag).
        $lastByte              = ord($decoded[strlen($decoded) - 1]);
        $decoded[strlen($decoded) - 1] = chr($lastByte ^ 0x01);

        $parts[2] = rtrim(strtr(base64_encode($decoded), '+/', '-_'), '=');

        return implode(':', $parts);
    }
}
