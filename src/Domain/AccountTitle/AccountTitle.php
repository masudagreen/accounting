<?php

declare(strict_types=1);

namespace Rucaro\Domain\AccountTitle;

use DateTimeImmutable;

/**
 * Account title (Chart of Accounts entry).
 *
 * Kept as a plain readonly DTO; validation of `category` and `normalSide`
 * happens at the DB layer (CHECK constraint) and in the upstream application
 * layer when creating new titles.
 */
final readonly class AccountTitle
{
    public const CATEGORIES = ['asset', 'liability', 'equity', 'revenue', 'expense'];
    public const NORMAL_SIDES = ['debit', 'credit'];

    public function __construct(
        public string $id,
        public string $entityId,
        public string $code,
        public string $name,
        public string $category,
        public string $normalSide,
        public ?string $parentId,
        public int $sortOrder,
        public bool $isActive,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
