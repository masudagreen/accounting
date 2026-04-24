<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\StatementOfChangesInEquity;

use PDO;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see SsManualAdjustmentRepositoryInterface} for the
 * `ss_manual_adjustments` table introduced by migration 0017.
 *
 * Uses `INSERT ... ON DUPLICATE KEY UPDATE` so repeated `save()`
 * calls against the same id mutate instead of duplicating — callers
 * get update-if-exists semantics without pre-selecting.
 */
final class PdoSsManualAdjustmentRepository implements SsManualAdjustmentRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(SsManualAdjustment $adjustment): void
    {
        $sql = <<<'SQL'
            INSERT INTO ss_manual_adjustments (
                id, entity_id, fiscal_term_id, section_code, change_type_code,
                amount, label, sort_order, notes
            ) VALUES (
                :id, :entity, :ft, :section, :change_type,
                :amount, :label, :sort_order, :notes
            )
            ON DUPLICATE KEY UPDATE
                section_code = VALUES(section_code),
                change_type_code = VALUES(change_type_code),
                amount = VALUES(amount),
                label = VALUES(label),
                sort_order = VALUES(sort_order),
                notes = VALUES(notes)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'          => UlidGenerator::decode($adjustment->id),
            ':entity'      => UlidGenerator::decode($adjustment->entityId),
            ':ft'          => UlidGenerator::decode($adjustment->fiscalTermId),
            ':section'     => $adjustment->sectionCode->value,
            ':change_type' => $adjustment->changeType->value,
            ':amount'      => $adjustment->amount,
            ':label'       => $adjustment->label,
            ':sort_order'  => $adjustment->sortOrder,
            ':notes'       => $adjustment->notes,
        ]);
    }

    public function findById(string $id): ?SsManualAdjustment
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ss_manual_adjustments WHERE id = :id LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ss_manual_adjustments
             WHERE entity_id = :e AND fiscal_term_id = :f
             ORDER BY sort_order ASC, id ASC',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':f' => UlidGenerator::decode($fiscalTermId),
        ]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->hydrate($row);
        }
        return $out;
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM ss_manual_adjustments WHERE id = :id',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): SsManualAdjustment
    {
        $sectionRaw = is_string($row['section_code'] ?? null)
            ? (string) $row['section_code']
            : '';
        $changeRaw = is_string($row['change_type_code'] ?? null)
            ? (string) $row['change_type_code']
            : '';

        $section = SsSectionCode::tryFrom($sectionRaw) ?? SsSectionCode::RetainedEarnings;
        $change  = SsChangeType::tryFrom($changeRaw) ?? SsChangeType::Other;

        return new SsManualAdjustment(
            id: self::encodeId($row['id'] ?? ''),
            entityId: self::encodeId($row['entity_id'] ?? ''),
            fiscalTermId: self::encodeId($row['fiscal_term_id'] ?? ''),
            sectionCode: $section,
            changeType: $change,
            amount: (string) ($row['amount'] ?? '0.0000'),
            label: (string) ($row['label'] ?? ''),
            sortOrder: (int) ($row['sort_order'] ?? 0),
            notes: self::nullableString($row['notes'] ?? null),
        );
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
}
