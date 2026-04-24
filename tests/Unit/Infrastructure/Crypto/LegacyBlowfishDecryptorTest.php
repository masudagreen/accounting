<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Crypto;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Crypto\Exception\CryptoException;
use Rucaro\Infrastructure\Crypto\LegacyBlowfishDecryptor;

/**
 * Byte-exact round-trip verification: we encrypt sample plaintexts with the
 * reference recipe from docs/phase1/encrypted-columns.md §4 (directly via
 * openssl_encrypt) and then feed the resulting raw BLOBs into
 * LegacyBlowfishDecryptor. If the decryptor is wrong by a single byte (key
 * slicing, IV derivation, padding treatment, trimming), the assertion fails.
 *
 * Because OpenSSL 3.x disables the legacy "bf-cbc" cipher by default, every
 * test here skips cleanly when the runtime lacks the legacy provider. That
 * matches the documented ops requirement (OPENSSL_CONF=.../openssl-legacy.cnf).
 */
#[CoversClass(LegacyBlowfishDecryptor::class)]
#[CoversClass(CryptoException::class)]
final class LegacyBlowfishDecryptorTest extends TestCase
{
    private const MASTER_SECRET = 'test-secret-123';

    protected function setUp(): void
    {
        if (!LegacyBlowfishDecryptor::isAvailable()) {
            self::markTestSkipped(
                'OpenSSL build does not expose bf-cbc. Enable the legacy provider '
                . '(e.g. OPENSSL_CONF pointing to an openssl.cnf with "legacy = legacy_sect" '
                . 'activated) to run these compatibility tests.',
            );
        }
    }

    /**
     * Mirrors the legacy recipe exactly. Lives in the test file so the
     * fixture generation is visible at the point of assertion — if the
     * legacy algorithm is ever misremembered, both sides of the test fail
     * together (which is the correct failure mode for a byte-exact claim).
     */
    private static function legacyEncrypt(string $plaintext, string $masterSecret): string
    {
        $key = substr(md5($masterSecret), 0, 56);
        $iv  = substr(md5($key), 0, 8);

        $blob = openssl_encrypt(
            $plaintext,
            'bf-cbc',
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv,
        );
        self::assertNotFalse($blob, 'legacy fixture generation must succeed');

        return $blob;
    }

    /**
     * Pad the plaintext with NUL bytes up to the next 8-byte Blowfish block.
     * Needed because OPENSSL_ZERO_PADDING demands block-aligned input on
     * encrypt (the legacy PHP mcrypt call did this implicitly).
     */
    private static function zeroPad(string $plaintext): string
    {
        $blockSize = 8;
        $remainder = strlen($plaintext) % $blockSize;
        if ($remainder === 0) {
            return $plaintext;
        }

        return $plaintext . str_repeat("\0", $blockSize - $remainder);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function fixturePlaintextProvider(): iterable
    {
        // Covers every real use-case documented in encrypted-columns.md §1:
        //   - ASCII password (strPassword)
        //   - UTF-8 JSON (blobDetail, blobData)
        //   - Long blob (up to 10kB)
        yield 'short ascii password'
            => ['pa55w0rd'];
        yield 'longer ascii password'
            => ['S3cret-P@ssphrase-With-Numbers-1234567890'];
        yield 'utf8 japanese json'
            => ['{"bank":"三菱UFJ","id":"user01","pin":"1234","備考":"テスト"}'];
        yield 'long blob'
            => [str_repeat('ABCDEFGH12345678', 256)]; // 4096 bytes, block-aligned
        yield 'exactly one block'
            => ['8bytesXX']; // 8 bytes == 1 block
    }

    #[DataProvider('fixturePlaintextProvider')]
    public function testDecryptsKnownPlaintextCiphertextPair(string $plaintext): void
    {
        $padded     = self::zeroPad($plaintext);
        $ciphertext = self::legacyEncrypt($padded, self::MASTER_SECRET);

        $decryptor = new LegacyBlowfishDecryptor(self::MASTER_SECRET);

        self::assertSame($plaintext, $decryptor->decrypt($ciphertext));
    }

    public function testTrimsTrailingNullBytesIntroducedByZeroPadding(): void
    {
        // 10-byte plaintext -> padded to 16 bytes (two blocks) with 6 NULs.
        // The decryptor must strip them back off.
        $plaintext  = 'abcdefghij';
        $ciphertext = self::legacyEncrypt(self::zeroPad($plaintext), self::MASTER_SECRET);

        $decryptor = new LegacyBlowfishDecryptor(self::MASTER_SECRET);

        self::assertSame($plaintext, $decryptor->decrypt($ciphertext));
    }

    public function testAadParameterIsIgnored(): void
    {
        // Legacy format predates AEAD. Passing a non-empty AAD must NOT
        // change the result, to keep the CipherInterface shape consistent.
        $plaintext  = 'hello world';
        $ciphertext = self::legacyEncrypt(self::zeroPad($plaintext), self::MASTER_SECRET);

        $decryptor = new LegacyBlowfishDecryptor(self::MASTER_SECRET);

        self::assertSame($plaintext, $decryptor->decrypt($ciphertext, 'any/aad/is/ignored'));
    }

    public function testDecryptFailsWithWrongSecret(): void
    {
        $ciphertext = self::legacyEncrypt(self::zeroPad('payload'), self::MASTER_SECRET);

        // Blowfish-CBC with the wrong key almost always produces garbage
        // bytes rather than throwing — so we only assert "output != plaintext".
        // For the 'empty input' corner case, however, openssl_decrypt does
        // return false, which the class rethrows as CryptoException. Either
        // outcome is acceptable; we just need to confirm it does NOT leak the
        // original plaintext.
        $decryptor = new LegacyBlowfishDecryptor('wrong-secret');

        try {
            $result = $decryptor->decrypt($ciphertext);
            self::assertNotSame('payload', $result);
        } catch (CryptoException) {
            // Acceptable: OpenSSL refused outright.
            self::assertTrue(true);
        }
    }

    public function testDecryptThrowsOnMalformedCiphertext(): void
    {
        // Blowfish block size is 8 bytes; any input whose length is not a
        // multiple of 8 must be rejected by openssl_decrypt.
        $decryptor = new LegacyBlowfishDecryptor(self::MASTER_SECRET);

        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('Legacy Blowfish decryption failed');

        $decryptor->decrypt("\x01\x02\x03");
    }

    public function testEncryptAlwaysThrows(): void
    {
        $decryptor = new LegacyBlowfishDecryptor(self::MASTER_SECRET);

        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('read-only');

        $decryptor->encrypt('anything');
    }

    public function testConstructorRejectsEmptySecret(): void
    {
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage('must not be empty');

        new LegacyBlowfishDecryptor('');
    }
}
