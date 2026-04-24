<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;

/**
 * In-memory {@see BlueReturnRepositoryInterface} for unit tests.
 */
final class InMemoryBlueReturnRepository implements BlueReturnRepositoryInterface
{
    /** @var array<string, BlueReturnForm> */
    private array $byId = [];

    public function save(BlueReturnForm $form): void
    {
        $this->byId[$form->id] = $form;
    }

    public function findById(string $id): ?BlueReturnForm
    {
        $form = $this->byId[$id] ?? null;
        if ($form === null || $form->deletedAt !== null) {
            return null;
        }
        return $form;
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): ?BlueReturnForm
    {
        foreach ($this->byId as $f) {
            if ($f->entityId === $entityId
                && $f->fiscalTermId === $fiscalTermId
                && $f->deletedAt === null) {
                return $f;
            }
        }
        return null;
    }

    public function findByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        bool $includeDeleted = false,
    ): array {
        $out = [];
        foreach ($this->byId as $f) {
            if ($f->entityId !== $entityId) {
                continue;
            }
            if ($fiscalTermId !== null && $f->fiscalTermId !== $fiscalTermId) {
                continue;
            }
            if (!$includeDeleted && $f->deletedAt !== null) {
                continue;
            }
            $out[] = $f;
        }
        return array_values($out);
    }

    public function delete(string $id): void
    {
        $existing = $this->byId[$id] ?? null;
        if ($existing === null || $existing->deletedAt !== null) {
            return;
        }
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->byId[$id] = new BlueReturnForm(
            id: $existing->id,
            entityId: $existing->entityId,
            fiscalTermId: $existing->fiscalTermId,
            formType: $existing->formType,
            status: $existing->status,
            snapshot: $existing->snapshot,
            finalizedAt: $existing->finalizedAt,
            createdBy: $existing->createdBy,
            createdAt: $existing->createdAt,
            updatedAt: $now,
            deletedAt: $now,
        );
    }
}
