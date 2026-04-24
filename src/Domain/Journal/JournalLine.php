<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * One line of a journal entry.
 *
 * Amounts are stored as strings to preserve DECIMAL(18,4) precision end-to-end
 * (matching ADR-002). All arithmetic goes through BCMath in the
 * {@see Journal} aggregate so float drift never leaks in.
 */
final readonly class JournalLine
{
    public const SIDE_DEBIT = 'debit';
    public const SIDE_CREDIT = 'credit';

    public function __construct(
        public ?string $id,
        public int $lineNo,
        public string $side,
        public string $accountTitleId,
        public ?string $subAccountTitleId,
        public string $amount,
        public string $taxRatePercent,
        public string $taxAmount,
        public bool $isTaxReduced,
        public string $memo,
        public DateTimeImmutable $bookedAt,
    ) {
        if ($lineNo < 1) {
            throw ValidationException::withErrors([
                sprintf('lines[%d].lineNo', $lineNo) => ['lineNo must be >= 1'],
            ]);
        }
        if ($side !== self::SIDE_DEBIT && $side !== self::SIDE_CREDIT) {
            throw ValidationException::withErrors([
                sprintf('lines[%d].side', $lineNo) => ["side must be 'debit' or 'credit'"],
            ]);
        }
        if (!preg_match('/^-?\d{1,14}(\.\d{1,4})?$/', $amount)) {
            throw ValidationException::withErrors([
                sprintf('lines[%d].amount', $lineNo) => ['amount must match DECIMAL(18,4) format'],
            ]);
        }
        if (Decimal::compare($amount, '0.0000') < 0) {
            throw ValidationException::withErrors([
                sprintf('lines[%d].amount', $lineNo) => ['amount must be >= 0'],
            ]);
        }
    }

    public function isDebit(): bool
    {
        return $this->side === self::SIDE_DEBIT;
    }

    public function isCredit(): bool
    {
        return $this->side === self::SIDE_CREDIT;
    }
}
