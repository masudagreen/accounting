<?php

declare(strict_types=1);

namespace Rucaro\Application\FiscalTerm;

use DateTimeImmutable;
use DateTimeZone;

final class FiscalTermValidator
{
    public const MAX_PERIOD = 9999;

    /**
     * @return array{errors: array<string, list<string>>, startDate: ?DateTimeImmutable, endDate: ?DateTimeImmutable}
     */
    public static function validate(int $fiscalPeriod, string $startDate, string $endDate): array
    {
        /** @var array<string, list<string>> $errors */
        $errors = [];
        if ($fiscalPeriod < 1 || $fiscalPeriod > self::MAX_PERIOD) {
            $errors['fiscal_period'][] = '期番号は 1 以上 ' . self::MAX_PERIOD . ' 以下で入力してください。';
        }

        $utc = new DateTimeZone('UTC');
        $start = self::parseDate($startDate, $utc);
        $end = self::parseDate($endDate, $utc);
        if ($start === null) {
            $errors['start_date'][] = '開始日は YYYY-MM-DD 形式で入力してください。';
        }
        if ($end === null) {
            $errors['end_date'][] = '終了日は YYYY-MM-DD 形式で入力してください。';
        }
        if ($start !== null && $end !== null && $end < $start) {
            $errors['end_date'][] = '終了日は開始日以降の日付にしてください。';
        }

        return ['errors' => $errors, 'startDate' => $start, 'endDate' => $end];
    }

    private static function parseDate(string $raw, DateTimeZone $tz): ?DateTimeImmutable
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, $tz);
        } catch (\Exception) {
            return null;
        }
    }
}
