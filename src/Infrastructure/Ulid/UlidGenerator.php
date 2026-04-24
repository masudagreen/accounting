<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Ulid;

use DateTimeImmutable;
use InvalidArgumentException;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * ULID (Universally Unique Lexicographically Sortable Identifier) generator.
 *
 * Implementation notes:
 *   - Layout: 48-bit unix-ms timestamp + 80-bit randomness, big-endian.
 *   - Text representation: 26 chars Crockford Base32 (`0123456789ABCDEFGHJKMNPQRSTVWXYZ`).
 *   - Binary representation: 16 raw bytes (suitable for MariaDB `BINARY(16)`).
 *
 * Not cryptographically a token; use {@see Rucaro\Infrastructure\Auth\BearerTokenGenerator}
 * for auth secrets.
 */
final class UlidGenerator
{
    private const ENCODE_ALPHABET = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    // 32-entry decode table; -1 = invalid.
    private const DECODE_MAP = [
        '0' => 0,  '1' => 1,  '2' => 2,  '3' => 3,  '4' => 4,  '5' => 5,  '6' => 6,  '7' => 7,
        '8' => 8,  '9' => 9,  'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15,
        'G' => 16, 'H' => 17, 'J' => 18, 'K' => 19, 'M' => 20, 'N' => 21, 'P' => 22, 'Q' => 23,
        'R' => 24, 'S' => 25, 'T' => 26, 'V' => 27, 'W' => 28, 'X' => 29, 'Y' => 30, 'Z' => 31,
    ];

    public function __construct(
        private readonly ClockInterface $clock = new SystemClock(),
    ) {
    }

    /**
     * Generate a new ULID as a 26-character Crockford Base32 string.
     */
    public function generate(): string
    {
        return self::encode($this->binary());
    }

    /**
     * Generate 16 raw bytes.
     */
    public function binary(): string
    {
        $now = $this->clock->getCurrentTime();
        return self::buildBinary($now, random_bytes(10));
    }

    /**
     * Build a binary ULID from a time and a 10-byte random sequence.
     * Exposed for deterministic testing.
     */
    public static function buildBinary(DateTimeImmutable $time, string $randomness): string
    {
        if (strlen($randomness) !== 10) {
            throw new InvalidArgumentException('randomness must be exactly 10 bytes');
        }
        $ms = (int) ($time->format('U') * 1000 + (int) $time->format('v'));
        // Pack 48-bit ms big-endian into 6 bytes (PHP lacks J8 big-endian for 6 byte, so split).
        $high = intdiv($ms, 0x100000000); // top 16 bits (ms can be 48 bits)
        $low = $ms & 0xFFFFFFFF;
        $prefix = pack('n', $high & 0xFFFF) . pack('N', $low);
        return $prefix . $randomness;
    }

    /**
     * Encode 16 raw bytes to a 26-char Crockford Base32 ULID string.
     */
    public static function encode(string $binary): string
    {
        if (strlen($binary) !== 16) {
            throw new InvalidArgumentException('ULID binary form must be exactly 16 bytes');
        }

        // Convert bytes to an array of unsigned integers.
        $bytes = array_values(unpack('C*', $binary) ?: []);
        // 26 chars = 130 bits, we only have 128 bits. The first char encodes
        // 3 bits (and 2 high bits must be 0).
        $out = '';

        // First char: top 3 bits of byte 0.
        $out .= self::ENCODE_ALPHABET[($bytes[0] & 0xE0) >> 5];
        // Next 5 bits of byte 0.
        $out .= self::ENCODE_ALPHABET[$bytes[0] & 0x1F];
        // From here we consume 5 bits at a time across bytes[1..15].
        // A compact loop is easier: we treat the remaining 15 bytes (120 bits)
        // as a bit stream and consume 24 groups of 5 bits.
        $bitBuffer = 0;
        $bitCount = 0;
        for ($i = 1; $i < 16; $i++) {
            $bitBuffer = ($bitBuffer << 8) | $bytes[$i];
            $bitCount += 8;
            while ($bitCount >= 5) {
                $bitCount -= 5;
                $out .= self::ENCODE_ALPHABET[($bitBuffer >> $bitCount) & 0x1F];
            }
        }
        return $out;
    }

    /**
     * Decode a 26-char Crockford Base32 ULID string back to 16 raw bytes.
     */
    public static function decode(string $ulid): string
    {
        if (strlen($ulid) !== 26) {
            throw new InvalidArgumentException('ULID string must be exactly 26 characters long');
        }
        $upper = strtoupper($ulid);
        $firstChar = $upper[0];
        if (!isset(self::DECODE_MAP[$firstChar]) || self::DECODE_MAP[$firstChar] > 7) {
            throw new InvalidArgumentException('ULID first character is out of range');
        }

        // Build the bits into 16 bytes.
        $bits = [];
        for ($i = 0; $i < 26; $i++) {
            $ch = $upper[$i];
            if (!isset(self::DECODE_MAP[$ch])) {
                throw new InvalidArgumentException('ULID contains invalid character: ' . $ch);
            }
            $bits[] = self::DECODE_MAP[$ch];
        }

        // First char = 3 bits (top-padded with 2 zero bits). Following = 5 bits each.
        // Output is 128 bits = 16 bytes.
        $bytes = array_fill(0, 16, 0);

        // Pack into a big-endian 128-bit integer using string bits.
        $bitString = str_pad(decbin($bits[0]), 3, '0', STR_PAD_LEFT);
        for ($i = 1; $i < 26; $i++) {
            $bitString .= str_pad(decbin($bits[$i]), 5, '0', STR_PAD_LEFT);
        }
        // bitString is now 3 + 25*5 = 128 chars.
        for ($i = 0; $i < 16; $i++) {
            /** @var numeric-string $segment */
            $segment = substr($bitString, $i * 8, 8);
            $bytes[$i] = (int) bindec($segment);
        }
        return pack('C*', ...$bytes);
    }

    /**
     * Validate the textual form.
     */
    public static function isValid(string $ulid): bool
    {
        if (strlen($ulid) !== 26) {
            return false;
        }
        $upper = strtoupper($ulid);
        if (!isset(self::DECODE_MAP[$upper[0]]) || self::DECODE_MAP[$upper[0]] > 7) {
            return false;
        }
        for ($i = 0; $i < 26; $i++) {
            if (!isset(self::DECODE_MAP[$upper[$i]])) {
                return false;
            }
        }
        return true;
    }
}
