<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatementNotes;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see FsNoteRepositoryInterface}.
 *
 * Binary(16) ULIDs are transparently decoded/encoded via
 * {@see UlidGenerator::decode()} / {@see UlidGenerator::encode()} so the
 * callers see 26-char Crockford text IDs on every boundary.
 */
final class PdoFsNoteRepository implements FsNoteRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(FinancialStatementNote $note): void
    {
        $sql = <<<'SQL'
            INSERT INTO fs_notes (
                id, entity_id, fiscal_term_id, template_code,
                category, label, body, sort_order, is_active,
                created_at, updated_at
            ) VALUES (
                :id, :entity, :ft, :tpl,
                :cat, :label, :body, :so, :active,
                :created_at, :updated_at
            )
            ON DUPLICATE KEY UPDATE
                template_code = VALUES(template_code),
                category      = VALUES(category),
                label         = VALUES(label),
                body          = VALUES(body),
                sort_order    = VALUES(sort_order),
                is_active     = VALUES(is_active),
                updated_at    = VALUES(updated_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'         => UlidGenerator::decode($note->id),
            ':entity'     => UlidGenerator::decode($note->entityId),
            ':ft'         => UlidGenerator::decode($note->fiscalTermId),
            ':tpl'        => $note->templateCode,
            ':cat'        => $note->category->value,
            ':label'      => $note->label,
            ':body'       => $note->body,
            ':so'         => $note->sortOrder,
            ':active'     => $note->isActive ? 1 : 0,
            ':created_at' => $note->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $note->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function findById(string $id): ?FinancialStatementNote
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fs_notes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntityAndTerm(
        string $entityId,
        string $fiscalTermId,
        bool $onlyActive = false,
    ): array {
        $sql = 'SELECT * FROM fs_notes WHERE entity_id = :e AND fiscal_term_id = :f';
        $params = [
            ':e' => UlidGenerator::decode($entityId),
            ':f' => UlidGenerator::decode($fiscalTermId),
        ];
        if ($onlyActive) {
            $sql .= ' AND is_active = 1';
        }
        $sql .= ' ORDER BY sort_order ASC, id ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    public function countByTemplateCode(
        string $entityId,
        string $fiscalTermId,
        string $templateCode,
    ): int {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) AS c FROM fs_notes WHERE entity_id = :e AND fiscal_term_id = :f AND template_code = :t',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':f' => UlidGenerator::decode($fiscalTermId),
            ':t' => $templateCode,
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return 0;
        }
        return (int) ($row['c'] ?? 0);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM fs_notes WHERE id = :id');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): FinancialStatementNote
    {
        $category = FsNoteCategory::tryFrom(
            is_string($row['category'] ?? null) ? (string) $row['category'] : 'other',
        ) ?? FsNoteCategory::Other;
        return new FinancialStatementNote(
            id: self::encodeId($row['id'] ?? ''),
            entityId: self::encodeId($row['entity_id'] ?? ''),
            fiscalTermId: self::encodeId($row['fiscal_term_id'] ?? ''),
            templateCode: self::nullableString($row['template_code'] ?? null),
            category: $category,
            label: (string) ($row['label'] ?? ''),
            body: (string) ($row['body'] ?? ''),
            sortOrder: (int) ($row['sort_order'] ?? 0),
            isActive: (int) ($row['is_active'] ?? 1) === 1,
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? self::now(),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? self::now(),
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
