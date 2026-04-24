<?php

declare(strict_types=1);

namespace Rucaro\Domain\Entity;

use DateTimeImmutable;

/**
 * Accounting entity (company or sole proprietor).
 *
 * `ownerUserId` links back to {@see \Rucaro\Domain\User\User::$id}. For now
 * only the owner can reach their entities; Phase 5+ will introduce sharing.
 */
final readonly class Entity
{
    public function __construct(
        public string $id,
        public string $ownerUserId,
        public string $name,
        public string $nationCode,
        public string $currencyCode,
        public string $fiscalStartMmDd,
        public bool $isActive,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt = null,
        public bool $isCorporate = true,
    ) {
    }
}
