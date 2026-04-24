<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Pure, side-effect-free converters from legacy column values to
 * new schema values.
 *
 * Kept deliberately static so importers stay small and tests can cover
 * the conversion logic without any DB mocking.
 */
final class LegacyValueConverter
{
    /**
     * Convert legacy `stamp*` BIGINT (UNIX epoch seconds) to a
     * `Y-m-d H:i:s.u` UTC string compatible with MariaDB TIMESTAMP(6).
     *
     * 0 or negative values are treated as "absent" by the caller; this
     * function still formats them but returns the Unix epoch, so callers
     * should guard on $stamp > 0 before invoking when semantic NULL is
     * desired.
     */
    public static function stampToTimestamp(int $stamp): string
    {
        $dt = (new DateTimeImmutable('@' . $stamp))
            ->setTimezone(new DateTimeZone('UTC'));
        return $dt->format('Y-m-d H:i:s.u');
    }

    /**
     * Convert legacy `stamp*` BIGINT to a local DATE string (YYYY-MM-DD).
     *
     * Legacy stored epoch seconds but all business data is authored in
     * Asia/Tokyo. Using UTC here would shift 日付 by 9h around midnight.
     */
    public static function stampToDate(int $stamp, string $tz = 'Asia/Tokyo'): string
    {
        $dt = (new DateTimeImmutable('@' . $stamp))
            ->setTimezone(new DateTimeZone($tz));
        return $dt->format('Y-m-d');
    }

    /**
     * Split a legacy `arrComma*` column (comma-separated values wrapped in
     * leading+trailing commas) into a trimmed array.
     *
     * Examples:
     *   ",cash,"                 => ["cash"]
     *   ",cash,salaries,"        => ["cash", "salaries"]
     *   ""                       => []
     *   ",,"                     => []
     *   ",,cash,,"               => ["cash"]
     *
     * Empty tokens are discarded so the output lines up with the semantic
     * number of values, not the number of commas.
     *
     * @return list<string>
     */
    public static function splitCommaArray(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        $parts = explode(',', $raw);
        $out = [];
        foreach ($parts as $p) {
            $trimmed = trim($p);
            if ($trimmed === '') {
                continue;
            }
            $out[] = $trimmed;
        }
        return $out;
    }

    /**
     * Compute a DATE range for a fiscal term from the legacy
     * `accountingEntityJpn` columns.
     *
     * @return array{start: string, end: string}
     */
    public static function fiscalTermDates(
        int $beginningYear,
        int $beginningMonth,
        int $termMonths = 12,
    ): array {
        if ($beginningMonth < 1 || $beginningMonth > 12) {
            throw new InvalidArgumentException('fiscalTermDates: month must be 1..12');
        }
        if ($termMonths < 1 || $termMonths > 24) {
            throw new InvalidArgumentException('fiscalTermDates: termMonths out of range');
        }
        $startDate = sprintf('%04d-%02d-01', $beginningYear, $beginningMonth);
        $start = new DateTimeImmutable($startDate);
        $end = $start->modify('+' . $termMonths . ' months')->modify('-1 day');
        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ];
    }

    /**
     * Build a deterministic short `account_titles.code` from a sequence
     * number. The new schema constrains `code` to VARCHAR(16), but legacy
     * camelCase identifiers (e.g. "corporateInhabitantAndEnterpriseTax")
     * easily exceed that, so we synthesise `L<4-digit>` codes and preserve
     * the original string in `name`.
     */
    public static function syntheticAccountTitleCode(int $seq): string
    {
        if ($seq < 0 || $seq > 9999) {
            throw new InvalidArgumentException('syntheticAccountTitleCode: seq out of range');
        }
        return sprintf('L%04d', $seq);
    }
}
