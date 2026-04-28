<?php

declare(strict_types=1);

namespace App\Domain\FiscalPeriod;

/**
 * 会計期間。期首月/期間月数/期番号 を起点に期首日・期末日を計算する。
 *
 * 元実装の `accountingEntityJpn` の以下の列に対応:
 *  - numFiscalBeginningYear  → $beginningYear (期首が属する暦年)
 *  - numFiscalBeginningMonth → $beginningMonth (1〜12)
 *  - numFiscalTermMonth      → $termMonths (通常12, 不規則決算で異なる)
 *  - numFiscalPeriod         → $number (期番号, 1始まり)
 */
final readonly class FiscalPeriod
{
    private function __construct(
        private int $termMonths,
        private int $number,
        private \DateTimeImmutable $startDate,
        private \DateTimeImmutable $endDate,
    ) {
    }

    public static function of(
        int $beginningYear,
        int $beginningMonth,
        int $termMonths,
        int $number,
    ): self {
        if ($beginningMonth < 1 || $beginningMonth > 12) {
            throw new \InvalidArgumentException(
                sprintf('beginningMonth must be 1..12, got %d', $beginningMonth),
            );
        }
        if ($termMonths < 1 || $termMonths > 12) {
            throw new \InvalidArgumentException(
                sprintf('termMonths must be 1..12, got %d', $termMonths),
            );
        }
        if ($number < 1) {
            throw new \InvalidArgumentException(
                sprintf('number must be >= 1, got %d', $number),
            );
        }

        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $beginningYear, $beginningMonth));
        // 期末日 = 期首日 + termMonths月 - 1日
        $end = $start
            ->modify(sprintf('+%d months', $termMonths))
            ->modify('-1 day');

        return new self($termMonths, $number, $start, $end);
    }

    public function startDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function termMonths(): int
    {
        return $this->termMonths;
    }

    public function contains(\DateTimeImmutable $date): bool
    {
        $d = $date->format('Y-m-d');
        return $d >= $this->startDate->format('Y-m-d')
            && $d <= $this->endDate->format('Y-m-d');
    }

    /**
     * 翌期。期首月・月数は同じ。
     */
    public function next(): self
    {
        $nextStart = $this->startDate->modify(sprintf('+%d months', $this->termMonths));
        return self::of(
            beginningYear: (int) $nextStart->format('Y'),
            beginningMonth: (int) $nextStart->format('n'),
            termMonths: $this->termMonths,
            number: $this->number + 1,
        );
    }

    /**
     * 指定日を含めて期末月までの月数を返す (期中取得時の按分等に使用).
     * 期外の日付には例外を投げる.
     */
    public function monthsRemaining(\DateTimeImmutable $date): int
    {
        if (! $this->contains($date)) {
            throw new \DomainException(sprintf(
                'date %s is not in fiscal period %s..%s',
                $date->format('Y-m-d'),
                $this->startDate->format('Y-m-d'),
                $this->endDate->format('Y-m-d'),
            ));
        }
        $startMonth = (int) $date->format('Y') * 12 + (int) $date->format('n');
        $endMonth   = (int) $this->endDate->format('Y') * 12 + (int) $this->endDate->format('n');
        return $endMonth - $startMonth + 1;
    }
}
