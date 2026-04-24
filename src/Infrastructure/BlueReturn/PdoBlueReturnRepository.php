<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\BlueReturn;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;
use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see BlueReturnRepositoryInterface}.
 *
 * Snapshot is serialised to JSON and stored in a single LONGTEXT
 * column. See ADR-016 §2 for the rationale.
 */
final class PdoBlueReturnRepository implements BlueReturnRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(BlueReturnForm $form): void
    {
        $sql = <<<'SQL'
            INSERT INTO blue_return_forms (
                id, entity_id, fiscal_term_id, form_type, snapshot_json,
                status, finalized_at, created_by, created_at, updated_at, deleted_at
            ) VALUES (
                :id, :entity, :ft, :form_type, :snapshot,
                :status, :finalized_at, :created_by, :created_at, :updated_at, :deleted_at
            )
            ON DUPLICATE KEY UPDATE
                form_type     = VALUES(form_type),
                snapshot_json = VALUES(snapshot_json),
                status        = VALUES(status),
                finalized_at  = VALUES(finalized_at),
                updated_at    = VALUES(updated_at),
                deleted_at    = VALUES(deleted_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'           => UlidGenerator::decode($form->id),
            ':entity'       => UlidGenerator::decode($form->entityId),
            ':ft'           => UlidGenerator::decode($form->fiscalTermId),
            ':form_type'    => $form->formType->value,
            ':snapshot'     => self::encodeSnapshot($form->snapshot),
            ':status'       => $form->status->value,
            ':finalized_at' => $form->finalizedAt?->format('Y-m-d H:i:s.u'),
            ':created_by'   => UlidGenerator::decode($form->createdBy),
            ':created_at'   => $form->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at'   => $form->updatedAt->format('Y-m-d H:i:s.u'),
            ':deleted_at'   => $form->deletedAt?->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function findById(string $id): ?BlueReturnForm
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM blue_return_forms WHERE id = :id AND deleted_at IS NULL LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): ?BlueReturnForm
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM blue_return_forms WHERE entity_id = :e AND fiscal_term_id = :f AND deleted_at IS NULL LIMIT 1',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':f' => UlidGenerator::decode($fiscalTermId),
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function findByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        bool $includeDeleted = false,
    ): array {
        $sql = 'SELECT * FROM blue_return_forms WHERE entity_id = :e';
        $params = [':e' => UlidGenerator::decode($entityId)];
        if ($fiscalTermId !== null) {
            $sql .= ' AND fiscal_term_id = :f';
            $params[':f'] = UlidGenerator::decode($fiscalTermId);
        }
        if (!$includeDeleted) {
            $sql .= ' AND deleted_at IS NULL';
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE blue_return_forms SET deleted_at = CURRENT_TIMESTAMP(6) WHERE id = :id AND deleted_at IS NULL',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    private static function encodeSnapshot(BlueReturnSnapshot $snapshot): string
    {
        $json = json_encode(
            $snapshot->toArray(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );
        return $json === false ? '{}' : $json;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): BlueReturnForm
    {
        $rawSnapshot = (string) ($row['snapshot_json'] ?? '{}');
        /** @var mixed $decoded */
        $decoded = json_decode($rawSnapshot, true);
        $snapshot = is_array($decoded)
            ? BlueReturnSnapshot::fromArray($decoded)
            : BlueReturnSnapshot::empty(BlueReturnFormType::General);

        $formType = BlueReturnFormType::tryFrom(is_string($row['form_type'] ?? null) ? (string) $row['form_type'] : 'general')
            ?? BlueReturnFormType::General;
        $status = BlueReturnStatus::tryFrom(is_string($row['status'] ?? null) ? (string) $row['status'] : 'draft')
            ?? BlueReturnStatus::Draft;

        return new BlueReturnForm(
            id: self::encodeId($row['id'] ?? ''),
            entityId: self::encodeId($row['entity_id'] ?? ''),
            fiscalTermId: self::encodeId($row['fiscal_term_id'] ?? ''),
            formType: $formType,
            status: $status,
            snapshot: $snapshot,
            finalizedAt: self::parseTimestamp($row['finalized_at'] ?? null),
            createdBy: self::encodeId($row['created_by'] ?? ''),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? self::now(),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? self::now(),
            deletedAt: self::parseTimestamp($row['deleted_at'] ?? null),
        );
    }

    private static function encodeId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
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
