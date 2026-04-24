<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Multi;

use DateTimeImmutable;

/**
 * Minimal read model describing one fiscal term — id, period label, date range.
 *
 * The multi-period use case needs these three bits per term to run the
 * single-period generator and to stamp the column header; pulling them through
 * a tiny repository interface keeps the use case independent from MySQL
 * (tests use an in-memory fake).
 */
final readonly class FiscalTermMetadata
{
    public function __construct(
        public string $id,
        public string $label,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
    ) {
    }
}
