<?php

declare(strict_types=1);

namespace Rucaro\Domain\SubAccountTitle;

use DateTimeImmutable;

/**
 * Sub-account title (補助科目) row in a flat table under its parent
 * {@see \Rucaro\Domain\AccountTitle\AccountTitle}.
 *
 * Kept as a plain readonly DTO. Hierarchy enforcement happens at the DB level
 * (FK to `account_titles.id`) and unique (account_title_id, code).
 */
final readonly class SubAccountTitle
{
    public function __construct(
        public string $id,
        public string $accountTitleId,
        public string $code,
        public string $name,
        public int $sortOrder,
        public bool $isActive,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
