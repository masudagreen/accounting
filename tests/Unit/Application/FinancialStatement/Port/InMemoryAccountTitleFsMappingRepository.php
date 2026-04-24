<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Port;

use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMapping;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\FsKind;

/**
 * In-memory {@see AccountTitleFsMappingRepositoryInterface} for unit tests.
 */
final class InMemoryAccountTitleFsMappingRepository implements AccountTitleFsMappingRepositoryInterface
{
    /** @var array<string, list<AccountTitleFsMapping>> */
    private array $byEntity = [];

    public function seed(
        string $entityId,
        string $accountTitleId,
        FsKind $kind,
        string $sectionCode,
        int $sign = 1,
        int $sortOrder = 0,
        ?string $displayLabel = null,
    ): void {
        $this->byEntity[$entityId] ??= [];
        $this->byEntity[$entityId][] = new AccountTitleFsMapping(
            accountTitleId: $accountTitleId,
            kind: $kind,
            sectionCode: $sectionCode,
            sign: $sign,
            sortOrder: $sortOrder,
            displayLabel: $displayLabel,
        );
    }

    public function findAllByEntity(string $entityId): array
    {
        return $this->byEntity[$entityId] ?? [];
    }
}
