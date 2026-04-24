<?php

declare(strict_types=1);

namespace Rucaro\Domain\FiscalTerm;

use DateTimeImmutable;

/**
 * Accounting fiscal term (旧: baseTerm / 会計期).
 *
 * Readonly DTO. Date ordering is enforced by the DB CHECK constraint so this
 * class does not duplicate that invariant — the UseCases that construct new
 * terms from operator input still run validation before persisting.
 */
final readonly class FiscalTerm
{
    public function __construct(
        public string $id,
        public string $entityId,
        public int $fiscalPeriod,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
        public bool $isClosed,
        public ?DateTimeImmutable $closedAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
