<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\TrialBalance;

use DateTimeImmutable;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshot;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshotRepositoryInterface;

/**
 * In-memory {@see TrialBalanceSnapshotRepositoryInterface} used by the
 * application-layer tests. Records each call so tests can assert the
 * delete-before-save order on refresh.
 */
final class InMemoryTrialBalanceSnapshotRepository implements TrialBalanceSnapshotRepositoryInterface
{
    /** @var list<TrialBalanceSnapshot> */
    public array $saved = [];

    /** @var list<array{entityId:string, fiscalTermId:string, date:string}> */
    public array $deleted = [];

    /**
     * Ordered log of operations ("delete" or "save") so tests can verify that
     * a refresh wipes before writing.
     *
     * @var list<string>
     */
    public array $operationLog = [];

    public function saveAll(array $snapshots): void
    {
        foreach ($snapshots as $s) {
            $this->saved[] = $s;
        }
        $this->operationLog[] = 'save:' . count($snapshots);
    }

    public function deleteByMonth(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $monthEnd,
    ): void {
        $key = $monthEnd->format('Y-m-d');
        $this->saved = array_values(array_filter(
            $this->saved,
            static fn (TrialBalanceSnapshot $s): bool => !(
                $s->entityId === $entityId
                && $s->fiscalTermId === $fiscalTermId
                && $s->snapshotDate->format('Y-m-d') === $key
            ),
        ));
        $this->deleted[] = [
            'entityId'     => $entityId,
            'fiscalTermId' => $fiscalTermId,
            'date'         => $key,
        ];
        $this->operationLog[] = 'delete:' . $key;
    }

    public function findByMonth(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $monthEnd,
    ): array {
        $key = $monthEnd->format('Y-m-d');
        return array_values(array_filter(
            $this->saved,
            static fn (TrialBalanceSnapshot $s): bool => (
                $s->entityId === $entityId
                && $s->fiscalTermId === $fiscalTermId
                && $s->snapshotDate->format('Y-m-d') === $key
            ),
        ));
    }
}
