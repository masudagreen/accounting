<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\CashPlan;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanCategory;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see CashPlanRepositoryInterface}.
 *
 * Header and entries are persisted in a single transaction so readers
 * never observe a plan with a stale entry set.
 */
final class PdoCashPlanRepository implements CashPlanRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(CashPlan $plan): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->upsertHeader($plan);
            $this->pdo->prepare('DELETE FROM cash_plan_entries WHERE cash_plan_id = :p')->execute([
                ':p' => UlidGenerator::decode($plan->id),
            ]);
            foreach ($plan->entries as $entry) {
                $this->insertEntry($plan->id, $entry);
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function findById(string $id): ?CashPlan
    {
        $stmt = $this->pdo->prepare('SELECT * FROM cash_plans WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntityAndName(string $entityId, string $fiscalTermId, string $name): ?CashPlan
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM cash_plans WHERE entity_id = :e AND fiscal_term_id = :f AND name = :n AND deleted_at IS NULL LIMIT 1',
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

    public function findByEntity(string $entityId, ?string $fiscalTermId = null, bool $includeDeleted = false): array
    {
        $sql = 'SELECT * FROM cash_plans WHERE entity_id = :e';
        $params = [':e' => UlidGenerator::decode($entityId)];
        if ($fiscalTermId !== null) {
            $sql .= ' AND fiscal_term_id = :f';
            $params[':f'] = UlidGenerator::decode($fiscalTermId);
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
        $stmt = $this->pdo->prepare('UPDATE cash_plans SET deleted_at = CURRENT_TIMESTAMP(6) WHERE id = :id AND deleted_at IS NULL');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    private function upsertHeader(CashPlan $plan): void
    {
        $sql = <<<'SQL'
            INSERT INTO cash_plans (
                id, entity_id, fiscal_term_id, name, opening_balance, currency_code,
                notes, created_by, created_at, updated_at, deleted_at
            ) VALUES (
                :id, :entity, :ft, :name, :opening, :cur,
                :notes, :created_by, :created_at, :updated_at, :deleted_at
            )
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                opening_balance = VALUES(opening_balance),
                currency_code = VALUES(currency_code),
                notes = VALUES(notes),
                updated_at = VALUES(updated_at),
                deleted_at = VALUES(deleted_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'         => UlidGenerator::decode($plan->id),
            ':entity'     => UlidGenerator::decode($plan->entityId),
            ':ft'         => UlidGenerator::decode($plan->fiscalTermId),
            ':name'       => $plan->name,
            ':opening'    => $plan->openingBalance,
            ':cur'        => $plan->currencyCode,
            ':notes'      => $plan->notes,
            ':created_by' => UlidGenerator::decode($plan->createdBy),
            ':created_at' => $plan->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $plan->updatedAt->format('Y-m-d H:i:s.u'),
            ':deleted_at' => $plan->deletedAt?->format('Y-m-d H:i:s.u'),
        ]);
    }

    private function insertEntry(string $planId, CashPlanEntry $entry): void
    {
        $sql = <<<'SQL'
            INSERT INTO cash_plan_entries (
                id, cash_plan_id, category, label, sort_order,
                month_1, month_2, month_3, month_4, month_5, month_6,
                month_7, month_8, month_9, month_10, month_11, month_12, memo
            ) VALUES (
                :id, :plan, :cat, :label, :so,
                :m1, :m2, :m3, :m4, :m5, :m6,
                :m7, :m8, :m9, :m10, :m11, :m12, :memo
            )
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id'    => UlidGenerator::decode($entry->id),
            ':plan'  => UlidGenerator::decode($planId),
            ':cat'   => $entry->category->value,
            ':label' => $entry->label,
            ':so'    => $entry->sortOrder,
            ':memo'  => $entry->memo,
        ];
        for ($i = 1; $i <= CashPlanEntry::MONTHS; $i++) {
            $params[':m' . $i] = $entry->monthlyAmounts[$i - 1];
        }
        $stmt->execute($params);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): CashPlan
    {
        $planIdRaw = $row['id'] ?? '';
        $planId = is_string($planIdRaw) && strlen($planIdRaw) === 16
            ? UlidGenerator::encode($planIdRaw)
            : (string) $planIdRaw;

        $entries = $this->loadEntries($planId);

        return new CashPlan(
            id: $planId,
            entityId: self::encodeId($row['entity_id'] ?? ''),
            fiscalTermId: self::encodeId($row['fiscal_term_id'] ?? ''),
            name: (string) ($row['name'] ?? ''),
            openingBalance: (string) ($row['opening_balance'] ?? '0.0000'),
            currencyCode: (string) ($row['currency_code'] ?? 'JPY'),
            notes: self::nullableString($row['notes'] ?? null),
            entries: $entries,
            createdBy: self::encodeId($row['created_by'] ?? ''),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? self::now(),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? self::now(),
            deletedAt: self::parseTimestamp($row['deleted_at'] ?? null),
        );
    }

    /**
     * @return list<CashPlanEntry>
     */
    private function loadEntries(string $planId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM cash_plan_entries WHERE cash_plan_id = :p ORDER BY sort_order ASC, label ASC',
        );
        $stmt->execute([':p' => UlidGenerator::decode($planId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $categoryRaw = is_string($r['category'] ?? null) ? (string) $r['category'] : 'operating_in';
            $category = CashPlanCategory::tryFrom($categoryRaw) ?? CashPlanCategory::OperatingIn;
            /** @var list<string> $amounts */
            $amounts = [];
            for ($i = 1; $i <= CashPlanEntry::MONTHS; $i++) {
                $amounts[] = (string) ($r['month_' . $i] ?? '0.0000');
            }
            $out[] = new CashPlanEntry(
                id: self::encodeId($r['id'] ?? ''),
                cashPlanId: $planId,
                category: $category,
                label: (string) ($r['label'] ?? ''),
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
