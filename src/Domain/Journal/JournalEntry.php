<?php

declare(strict_types=1);

namespace App\Domain\Journal;

use App\Domain\Money\Money;

/**
 * 仕訳。借方明細と貸方明細から成る。
 *
 * 不変条件:
 *  1. 借方明細1件以上、貸方明細1件以上
 *  2. 借方合計 == 貸方合計
 *
 * 値オブジェクトに近い設計だが、識別 (id) を後段で持たせる想定 (永続化層で付与)。
 */
final readonly class JournalEntry
{
    /**
     * @param list<JournalLine> $debits
     * @param list<JournalLine> $credits
     */
    private function __construct(
        private array $debits,
        private array $credits,
        private Money $totalDebits,
        private Money $totalCredits,
    ) {
    }

    /**
     * @param list<JournalLine> $debits
     * @param list<JournalLine> $credits
     */
    public static function of(array $debits, array $credits): self
    {
        if (count($debits) === 0) {
            throw new \InvalidArgumentException('debits must contain at least one line');
        }
        if (count($credits) === 0) {
            throw new \InvalidArgumentException('credits must contain at least one line');
        }

        $totalDebits = self::sumLines($debits);
        $totalCredits = self::sumLines($credits);

        if (! $totalDebits->equals($totalCredits)) {
            throw UnbalancedJournalException::of($totalDebits, $totalCredits);
        }

        return new self($debits, $credits, $totalDebits, $totalCredits);
    }

    public function totalDebits(): Money
    {
        return $this->totalDebits;
    }

    public function totalCredits(): Money
    {
        return $this->totalCredits;
    }

    public function isBalanced(): bool
    {
        return $this->totalDebits->equals($this->totalCredits);
    }

    /** @return list<JournalLine> */
    public function debits(): array
    {
        return $this->debits;
    }

    /** @return list<JournalLine> */
    public function credits(): array
    {
        return $this->credits;
    }

    /**
     * @param list<JournalLine> $lines
     */
    private static function sumLines(array $lines): Money
    {
        $sum = Money::zero();
        foreach ($lines as $line) {
            $sum = $sum->plus($line->amount());
        }
        return $sum;
    }
}
