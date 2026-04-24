<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Port\Cs;

use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMapping;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsFlowCategory;

/**
 * In-memory {@see AccountTitleCsMappingRepositoryInterface} for unit tests.
 */
final class InMemoryAccountTitleCsMappingRepository implements AccountTitleCsMappingRepositoryInterface
{
    /** @var array<string, list<AccountTitleCsMapping>> */
    private array $byEntity = [];

    public function seed(
        string $entityId,
        string $accountTitleId,
        string $sectionCode,
        CsFlowCategory $flow,
        int $sign = 1,
        bool $isWorkingCapital = false,
        int $sortOrder = 0,
        ?string $displayLabel = null,
    ): void {
        $this->byEntity[$entityId] ??= [];
        $this->byEntity[$entityId][] = new AccountTitleCsMapping(
            accountTitleId: $accountTitleId,
            sectionCode: $sectionCode,
            flowCategory: $flow,
            sign: $sign,
            isWorkingCapital: $isWorkingCapital,
            sortOrder: $sortOrder,
            displayLabel: $displayLabel,
        );
    }

    public function findAllByEntity(string $entityId): array
    {
        return $this->byEntity[$entityId] ?? [];
    }
}
