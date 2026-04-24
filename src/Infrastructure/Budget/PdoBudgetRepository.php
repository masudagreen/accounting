<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Budget;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see BudgetRepositoryInterface}.
 *
 * Header + line items are persisted atomically; on save() we wipe the
 * existing line items and re-insert so callers never observe a mid-edit
 * partial row set.
 */
final class PdoBudgetRepository implements BudgetRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(Budget $budget): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->upsertHeader($budget);
            $this->pdo
                ->prepare('DELETE FROM budget_line_items WHERE budget_id = :b')
                ->execute([':b' => UlidGenerator::decode($budget->id)]);
            foreach ($budget->lineItems as $li) {
                $this->insertLineItem($budget->id, $li);
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function findById(string $id): ?Budget
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM budgets WHERE id = :id AND deleted_at IS NULL LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntityAndName(string $entityId, string $fiscalTermId, string $name): ?Budget
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM budgets WHERE entity_id = :e AND fiscal_term_id = :f AND name = :n AND deleted_at IS NULL LIMIT 1',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':f' => UlidGenerator::decode($fiscalTermId),
            ':n' => $name,
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        ?BudgetStatus $status = null,
        bool $includeDeleted = false,
    ): array {
        $sql = 'SELECT * FROM budgets WHERE entity_id = :e';
        $params = [':e' => UlidGenerator::decode($entityId)];
        if ($fiscalTermId !== null) {
            $sql .= ' AND fiscal_term_id = :f';
            $params[':f'] = UlidGenerator::decode($fiscalTermId);
        }
        if ($status !== null) {
            $sql .= ' AND status = :s';
            $params[':s'] = $status->value;
        }
        if (!$includeDeleted) {
            $sql .= ' AND deleted_at IS NULL';
        }
        $sql .= ' ORDER BY name ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE budgets SET deleted_at = CURRENT_TIMESTAMP(6) WHERE id = :id AND deleted_at IS NULL',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    private function upsertHeader(Budget $budget): void
    {
        $sql = <<<'SQL'
            INSERT INTO budgets (
                id, entity_id, fiscal_term_id, name, status,
                approved_by, approved_at, notes,
                created_by, created_at, updated_at, deleted_at
            ) VALUES (
                :id, :entity, :ft, :name, :status,
                :approved_by, :approved_at, :notes,
                :created_by, :created_at, :updated_at, :deleted_at
            )
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                status = VALUES(status),
                approved_by = VALUES(approved_by),
                approved_at = VALUES(approved_at),
                notes = VALUES(notes),
                updated_at = VALUES(updated_at),
                deleted_at = VALUES(deleted_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'          => UlidGenerator::decode($budget->id),
            ':entity'      => UlidGenerator::decode($budget->entityId),
            ':ft'          => UlidGenerator::decode($budget->fiscalTermId),
            ':name'        => $budget->name,
            ':status'      => $budget->status->value,
            ':approved_by' => $budget->approvedBy !== null ? UlidGenerator::decode($budget->approvedBy) : null,
            ':approved_at' => $budget->approvedAt?->format('Y-m-d H:i:s.u'),
            ':notes'       => $budget->notes,
            ':created_by'  => UlidGenerator::decode($budget->createdBy),
            ':created_at'  => $budget->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at'  => $budget->updatedAt->format('Y-m-d H:i:s.u'),
            ':deleted_at'  => $budget->deletedAt?->format('Y-m-d H:i:s.u'),
        ]);
    }

    private function insertLineItem(string $budgetId, BudgetLineItem $li): void
    {
        $sql = <<<'SQL'
            INSERT INTO budget_line_items (
                id, budget_id, account_title_id, sub_account_title_id, sort_order,
                month_1, month_2, month_3, month_4, month_5, month_6,
                month_7, month_8, month_9, month_10, month_11, month_12,
                memo
            ) VALUES (
                :id, :b, :at, :sat, :so,
                :m1, :m2, :m3, :m4, :m5, :m6,
                :m7, :m8, :m9, :m10, :m11, :m12,
                :memo
            )
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id'   => UlidGenerator::decode($li->id),
            ':b'    => UlidGenerator::decode($budgetId),
            ':at'   => UlidGenerator::decode($li->accountTitleId),
            ':sat'  => $li->subAccountTitleId !== null ? UlidGenerator::decode($li->subAccountTitleId) : null,
            ':so'   => $li->sortOrder,
            ':memo' => $li->memo,
        ];
        for ($i = 1; $i <= BudgetLineItem::MONTHS; $i++) {
            $params[':m' . $i] = $li->monthlyAmounts[$i - 1];
        }
        $stmt->execute($params);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Budget
    {
        $budgetId = self::encodeId($row['id'] ?? '');
        $status = BudgetStatus::tryFrom(is_string($row['status'] ?? null) ? (string) $row['status'] : 'draft')
            ?? BudgetStatus::Draft;

        return new Budget(
            id: $budgetId,
            entityId: self::encodeId($row['entity_id'] ?? ''),
            fiscalTermId: self::encodeId($row['fiscal_term_id'] ?? ''),
            name: (string) ($row['name'] ?? ''),
            status: $status,
            approvedBy: self::nullableEncodedId($row['approved_by'] ?? null),
            approvedAt: self::parseTimestamp($row['approved_at'] ?? null),
            notes: self::nullableString($row['notes'] ?? null),
            lineItems: $this->loadLineItems($budgetId),
            createdBy: self::encodeId($row['created_by'] ?? ''),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? self::now(),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? self::now(),
            deletedAt: self::parseTimestamp($row['deleted_at'] ?? null),
        );
    }

    /**
     * @return list<BudgetLineItem>
     */
    private function loadLineItems(string $budgetId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM budget_line_items WHERE budget_id = :b ORDER BY sort_order ASC, id ASC',
        );
        $stmt->execute([':b' => UlidGenerator::decode($budgetId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            /** @var list<string> $amounts */
            $amounts = [];
            for ($m = 1; $m <= BudgetLineItem::MONTHS; $m++) {
                $amounts[] = (string) ($r['month_' . $m] ?? '0.0000');
            }
            $out[] = new BudgetLineItem(
                id: self::encodeId($r['id'] ?? ''),
                budgetId: $budgetId,
                accountTitleId: self::encodeId($r['account_title_id'] ?? ''),
                subAccountTitleId: self::nullableEncodedId($r['sub_account_title_id'] ?? null),
                sortOrder: (int) ($r['sort_order'] ?? 0),
                monthlyAmounts: $amounts,
                memo: self::nullableString($r['memo'] ?? null),
            );
        }
        return $out;
    }

    private static function encodeId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function nullableEncodedId(mixed $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        if (!is_string($raw) || $raw === '') {
            return null;
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function nullableString(mixed $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $s = (string) $raw;
        return $s === '' ? null : $s;
    }

    private static function parseTimestamp(mixed $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '' || !is_string($raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
