<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use InvalidArgumentException;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Domain\Journal\ValueObject\JournalDate;

/**
 * Criteria object consumed by {@see \Rucaro\Domain\Journal\JournalRepositoryInterface::findByCriteria}
 * and the `SearchJournalUseCase`.
 *
 * Keeping the filter knobs grouped here, rather than a long parameter list,
 * lets new filters land without breaking every call site and gives us a
 * single place to document which combinations are legal.
 *
 * Phase 7-2 note: `sortBy` / `sortOrder` were added for the Web UI journal
 * list page. Values are pinned to the {@see SORT_BY_ALLOW_LIST} /
 * {@see SORT_ORDER_ALLOW_LIST} sets to prevent SQL injection when the
 * repository interpolates the column name into the ORDER BY clause.
 */
final readonly class JournalSearchCriteria
{
    public const SORT_BY_JOURNAL_DATE = 'journal_date';
    public const SORT_BY_SUMMARY      = 'summary';
    public const SORT_BY_TOTAL_AMOUNT = 'total_amount';
    public const SORT_BY_STATUS       = 'status';
    public const SORT_BY_CREATED_AT   = 'created_at';
    public const SORT_BY_CREATED_BY   = 'created_by';

    /** @var list<string> */
    public const SORT_BY_ALLOW_LIST = [
        self::SORT_BY_JOURNAL_DATE,
        self::SORT_BY_SUMMARY,
        self::SORT_BY_TOTAL_AMOUNT,
        self::SORT_BY_STATUS,
        self::SORT_BY_CREATED_AT,
        self::SORT_BY_CREATED_BY,
    ];

    public const SORT_ORDER_ASC  = 'asc';
    public const SORT_ORDER_DESC = 'desc';

    /** @var list<string> */
    public const SORT_ORDER_ALLOW_LIST = [
        self::SORT_ORDER_ASC,
        self::SORT_ORDER_DESC,
    ];

    public function __construct(
        public string $entityId,
        /** @var int<1, max> */
        public int $page = 1,
        /** @var int<1, max> */
        public int $pageSize = 50,
        public ?JournalDate $from = null,
        public ?JournalDate $to = null,
        public ?string $fiscalTermId = null,
        public ?string $accountTitleId = null,
        public ?JournalStatus $status = null,
        public ?string $source = null,
        public ?string $textQuery = null,
        public bool $includeTrashed = false,
        public string $sortBy = self::SORT_BY_JOURNAL_DATE,
        public string $sortOrder = self::SORT_ORDER_DESC,
    ) {
        if (!in_array($sortBy, self::SORT_BY_ALLOW_LIST, true)) {
            throw new InvalidArgumentException(sprintf(
                "sortBy must be one of: %s (got '%s')",
                implode(', ', self::SORT_BY_ALLOW_LIST),
                $sortBy,
            ));
        }
        if (!in_array($sortOrder, self::SORT_ORDER_ALLOW_LIST, true)) {
            throw new InvalidArgumentException(sprintf(
                "sortOrder must be one of: %s (got '%s')",
                implode(', ', self::SORT_ORDER_ALLOW_LIST),
                $sortOrder,
            ));
        }
    }

    /**
     * Normalize user-supplied sort params into an allow-listed pair. Unknown
     * inputs fall back to the defaults instead of raising — the UI should
     * never 500 on a rogue query string.
     *
     * @return array{0: string, 1: string}
     */
    public static function resolveSort(?string $sortBy, ?string $sortOrder): array
    {
        $byCandidate = $sortBy !== null ? strtolower($sortBy) : self::SORT_BY_JOURNAL_DATE;
        $orderCandidate = $sortOrder !== null ? strtolower($sortOrder) : self::SORT_ORDER_DESC;

        $by = in_array($byCandidate, self::SORT_BY_ALLOW_LIST, true)
            ? $byCandidate
            : self::SORT_BY_JOURNAL_DATE;
        $order = in_array($orderCandidate, self::SORT_ORDER_ALLOW_LIST, true)
            ? $orderCandidate
            : self::SORT_ORDER_DESC;
        return [$by, $order];
    }
}
