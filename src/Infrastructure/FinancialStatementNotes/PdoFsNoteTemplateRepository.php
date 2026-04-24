<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatementNotes;

use PDO;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplate;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplateRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Read-only {@see FsNoteTemplateRepositoryInterface} backed by fs_note_templates.
 */
final class PdoFsNoteTemplateRepository implements FsNoteTemplateRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM fs_note_templates ORDER BY category ASC, sort_order ASC, code ASC',
        );
        if ($stmt === false) {
            return [];
        }
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    public function findByCode(string $code): ?FsNoteTemplate
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fs_note_templates WHERE code = :c LIMIT 1');
        $stmt->execute([':c' => $code]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByCodes(array $codes): array
    {
        $codes = array_values(array_unique(array_filter($codes, static fn ($v) => is_string($v) && $v !== '')));
        if ($codes === []) {
            return [];
        }
        $placeholders = [];
        $params = [];
        foreach ($codes as $i => $c) {
            $key = ':c' . $i;
            $placeholders[] = $key;
            $params[$key] = $c;
        }
        $sql = 'SELECT * FROM fs_note_templates WHERE code IN (' . implode(',', $placeholders) . ')'
            . ' ORDER BY category ASC, sort_order ASC, code ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): FsNoteTemplate
    {
        $category = FsNoteCategory::tryFrom(
            is_string($row['category'] ?? null) ? (string) $row['category'] : 'other',
        ) ?? FsNoteCategory::Other;
        return new FsNoteTemplate(
            id: self::encodeId($row['id'] ?? ''),
            code: (string) ($row['code'] ?? ''),
            category: $category,
            label: (string) ($row['label'] ?? ''),
            defaultBody: (string) ($row['default_body'] ?? ''),
            sortOrder: (int) ($row['sort_order'] ?? 0),
        );
    }

    private static function encodeId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }
}
