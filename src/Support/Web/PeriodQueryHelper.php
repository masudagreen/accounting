<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Converts the Web UI's `?year=2025&month=12` query convention into a
 * `[fromDate, toDate]` DateTimeImmutable pair, clamped against the fiscal
 * term's own range.
 *
 * Resolution rules:
 *   - `year` + `month`  → the calendar month's 1st through last day,
 *                        clamped to the fiscal term's start / end.
 *   - `year` only       → Jan 1 – Dec 31 of that year, clamped.
 *   - neither provided  → the full fiscal term range.
 *
 * The fiscal term range is read from the `fiscal_terms` table via
 * {@see \PDO}. When the term cannot be resolved the helper returns a
 * conservative open window centred on Unix epoch / "now" so callers do
 * not have to branch on the null case.
 */
final class PeriodQueryHelper
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * @return array{0: DateTimeImmutable, 1: DateTimeImmutable, 2: ?DateTimeImmutable, 3: ?DateTimeImmutable}
     *         Tuple of (fromDate, toDate, termStart, termEnd). The last two
     *         entries are null when the fiscal term id did not resolve.
     */
    public function resolve(?string $fiscalTermId, ?int $year, ?int $month): array
    {
        [$termStart, $termEnd] = $fiscalTermId !== null
            ? $this->lookupFiscalTermBounds($fiscalTermId)
            : [null, null];

        $tz = new DateTimeZone('UTC');

        if ($year !== null && $month !== null) {
            $from = self::safeDate(sprintf('%04d-%02d-01', $year, $month), $tz);
            if ($from !== null) {
                $lastDay = (int) $from->format('t');
                $to = self::safeDate(sprintf('%04d-%02d-%02d', $year, $month, $lastDay), $tz) ?? $from;
                return [
                    self::max($from, $termStart),
                    self::min($to, $termEnd),
                    $termStart,
                    $termEnd,
                ];
            }
        }

        if ($year !== null) {
            $from = self::safeDate(sprintf('%04d-01-01', $year), $tz);
            $to = self::safeDate(sprintf('%04d-12-31', $year), $tz);
            if ($from !== null && $to !== null) {
                return [
                    self::max($from, $termStart),
                    self::min($to, $termEnd),
                    $termStart,
                    $termEnd,
                ];
            }
        }

        // Neither year nor month — fall back to the fiscal term range, or a
        // conservative fallback when the term is unknown.
        $from = $termStart ?? new DateTimeImmutable('1970-01-01', $tz);
        $to = $termEnd ?? new DateTimeImmutable('now', $tz);
        return [$from, $to, $termStart, $termEnd];
    }

    /**
     * Parses a positive 4-digit calendar year from a query string; returns
     * null when the input is missing or nonsensical.
     */
    public static function parseYear(?string $raw): ?int
    {
        if ($raw === null || $raw === '' || !ctype_digit($raw)) {
            return null;
        }
        $n = (int) $raw;
        if ($n < 1900 || $n > 2999) {
            return null;
        }
        return $n;
    }

    /**
     * Parses a calendar month (1-12) from a query string; returns null when
     * the input is missing, non-numeric, or out of range.
     */
    public static function parseMonth(?string $raw): ?int
    {
        if ($raw === null || $raw === '' || !ctype_digit($raw)) {
            return null;
        }
        $n = (int) $raw;
        if ($n < 1 || $n > 12) {
            return null;
        }
        return $n;
    }

    /**
     * Resolve the most recent fiscal term id for the given entity. Returns
     * null when the entity has no fiscal terms yet. Useful when the session
     * has no selected fiscal term — the UI defaults to the newest term.
     */
    public function findLatestFiscalTermId(string $entityId): ?string
    {
        if (!UlidGenerator::isValid($entityId)) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            'SELECT id FROM fiscal_terms WHERE entity_id = :eid '
            . 'ORDER BY start_date DESC LIMIT 1',
        );
        $stmt->execute([':eid' => UlidGenerator::decode($entityId)]);
        /** @var string|false $raw */
        $raw = $stmt->fetchColumn();
        if ($raw === false || !is_string($raw) || $raw === '') {
            return null;
        }
        return UlidGenerator::encode($raw);
    }

    /**
     * @return array{0: ?DateTimeImmutable, 1: ?DateTimeImmutable}
     */
    private function lookupFiscalTermBounds(string $fiscalTermId): array
    {
        if (!UlidGenerator::isValid($fiscalTermId)) {
            return [null, null];
        }
        $stmt = $this->pdo->prepare(
            'SELECT start_date, end_date FROM fiscal_terms WHERE id = :id LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var array<string, string>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return [null, null];
        }
        $tz = new DateTimeZone('UTC');
        return [
            self::safeDate((string) ($row['start_date'] ?? ''), $tz),
            self::safeDate((string) ($row['end_date'] ?? ''), $tz),
        ];
    }

    private static function safeDate(string $raw, DateTimeZone $tz): ?DateTimeImmutable
    {
        if ($raw === '' || !preg_match('/^\d{4}-\d{2}-\d{2}/', $raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable(substr($raw, 0, 10), $tz);
        } catch (\Exception) {
            return null;
        }
    }

    private static function max(DateTimeImmutable $a, ?DateTimeImmutable $b): DateTimeImmutable
    {
        if ($b === null) {
            return $a;
        }
        return $a > $b ? $a : $b;
    }

    private static function min(DateTimeImmutable $a, ?DateTimeImmutable $b): DateTimeImmutable
    {
        if ($b === null) {
            return $a;
        }
        return $a < $b ? $a : $b;
    }
}
