<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\BreakEvenPoint;

use PDO;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassificationRepositoryInterface;
use Rucaro\Domain\BreakEvenPoint\CvpCostType;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see AccountTitleCvpClassificationRepositoryInterface}.
 *
 * The UNIQUE KEY on `(entity_id, account_title_id)` lets us rely on
 * `INSERT ... ON DUPLICATE KEY UPDATE` for idempotent upserts. A bulk
 * `saveMany()` wraps the loop in a single transaction so partial writes
 * never leak through.
 */
final class PdoAccountTitleCvpClassificationRepository implements AccountTitleCvpClassificationRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly UlidGenerator $ulids,
    ) {
    }

    public function findAllByEntity(string $entityId): array
    {
        $sql = 'SELECT entity_id, account_title_id, cost_type, variable_ratio, notes
                FROM account_title_cvp_classifications
                WHERE entity_id = :e
                ORDER BY account_title_id ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':e' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_filter(array_map(
            fn (array $r): ?AccountTitleCvpClassification => $this->hydrate($r),
            $rows,
        )));
    }

    public function findByAccountTitle(string $entityId, string $accountTitleId): ?AccountTitleCvpClassification
    {
        $stmt = $this->pdo->prepare(
            'SELECT entity_id, account_title_id, cost_type, variable_ratio, notes
             FROM account_title_cvp_classifications
             WHERE entity_id = :e AND account_title_id = :a LIMIT 1',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':a' => UlidGenerator::decode($accountTitleId),
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function save(AccountTitleCvpClassification $classification): void
    {
        $sql = <<<'SQL'
            INSERT INTO account_title_cvp_classifications
                (id, entity_id, account_title_id, cost_type, variable_ratio, notes)
            VALUES
                (:id, :e, :a, :t, :r, :n)
            ON DUPLICATE KEY UPDATE
                cost_type = VALUES(cost_type),
                variable_ratio = VALUES(variable_ratio),
                notes = VALUES(notes)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => UlidGenerator::decode($this->ulids->generate()),
            ':e'  => UlidGenerator::decode($classification->entityId),
            ':a'  => UlidGenerator::decode($classification->accountTitleId),
            ':t'  => $classification->costType->value,
            ':r'  => $classification->variableRatio,
            ':n'  => $classification->notes,
        ]);
    }

    public function saveMany(array $classifications): void
    {
        if ($classifications === []) {
            return;
        }
        $ownTx = !$this->pdo->inTransaction();
        if ($ownTx) {
            $this->pdo->beginTransaction();
        }
        try {
            foreach ($classifications as $c) {
                $this->save($c);
            }
            if ($ownTx) {
                $this->pdo->commit();
            }
        } catch (\Throwable $e) {
            if ($ownTx && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function delete(string $entityId, string $accountTitleId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM account_title_cvp_classifications WHERE entity_id = :e AND account_title_id = :a',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':a' => UlidGenerator::decode($accountTitleId),
        ]);
    }

    /**
     * @param array<string, mixed> $r
     */
    private function hydrate(array $r): ?AccountTitleCvpClassification
    {
        $typeRaw = is_string($r['cost_type'] ?? null) ? (string) $r['cost_type'] : 'fixed';
        try {
            $type = CvpCostType::fromString($typeRaw);
        } catch (\InvalidArgumentException) {
            return null;
        }
        return new AccountTitleCvpClassification(
            entityId: self::encodeId($r['entity_id'] ?? ''),
            accountTitleId: self::encodeId($r['account_title_id'] ?? ''),
            costType: $type,
            variableRatio: (string) ($r['variable_ratio'] ?? '1.0000'),
            notes: self::nullableString($r['notes'] ?? null),
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
