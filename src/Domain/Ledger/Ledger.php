<?php

declare(strict_types=1);

namespace Rucaro\Domain\Ledger;

use DateTimeImmutable;

/**
 * General Ledger aggregate (総勘定元帳).
 *
 * Holds one {@see LedgerBook} per requested account title over a period.
 * When the caller asks for a single account the {@see $books} list is
 * size 1; when the caller asks for the whole entity the books are emitted
 * in account-code ascending order.
 *
 * Immutable — built once by the query layer and consumed unchanged by
 * serializers and renderers.
 */
final readonly class Ledger
{
    /**
     * @param list<LedgerBook> $books
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public string $currencyCode,
        public array $books,
        public DateTimeImmutable $generatedAt,
    ) {
    }
}
