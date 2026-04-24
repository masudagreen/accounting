<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\AccountTitle;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoAccountTitleRepository implements AccountTitleRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function listByEntity(
        string $entityId,
        int $page,
        int $pageSize,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): array {
        [$where, $params] = $this->buildFilter($entityId, $category, $isActive, $search);
        $offset = ($page - 1) * $pageSize;
        $sql = 'SELECT id, entity_id, code, name, category, normal_side, parent_id,
                       sort_order, is_active, created_at, updated_at
                FROM account_titles
                WHERE ' . $where . '
                ORDER BY sort_order ASC, code ASC
                LIMIT :_limit OFFSET :_offset';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':_limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    public function countByEntity(
        string $entityId,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): int {
        [$where, $params] = $this->buildFilter($entityId, $category, $isActive, $search);
        $sql = 'SELECT COUNT(*) FROM account_titles WHERE ' . $where;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        /** @var string|false $c */
        $c = $stmt->fetchColumn();
        return $c === false ? 0 : (int) $c;
    }

    public function findAllByEntity(string $entityId): array
    {
        $sql = 'SELECT id, entity_id, code, name, category, normal_side, parent_id,
                       sort_order, is_active, created_at, updated_at
                FROM account_titles
                WHERE entity_id = :entity AND deleted_at IS NULL
                ORDER BY sort_order ASC, code ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findById(string $id): ?AccountTitle
    {
        $sql = 'SELECT id, entity_id, code, name, category, normal_side, parent_id,
                       sort_order, is_active, created_at, updated_at
                FROM account_titles
                WHERE id = :id AND deleted_at IS NULL
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function save(AccountTitle $title): void
    {
        $sql = 'INSERT INTO account_titles
                    (id, entity_id, code, name, category, normal_side, parent_id,
                     sort_order, is_active, created_at, updated_at)
                VALUES
                    (:id, :entity, :code, :name, :category, :normal_side, :parent,
                     :sort_order, :is_active, :created_at, :updated_at)
                ON DUPLICATE KEY UPDATE
                    code = VALUES(code),
                    name = VALUES(name),
                    category = VALUES(category),
                    normal_side = VALUES(normal_side),
                    parent_id = VALUES(parent_id),
                    sort_order = VALUES(sort_order),
                    is_active = VALUES(is_active),
                    updated_at = VALUES(updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'           => UlidGenerator::decode($title->id),
            ':entity'       => UlidGenerator::decode($title->entityId),
            ':code'         => $title->code,
            ':name'         => $title->name,
            ':category'     => $title->category,
            ':normal_side'  => $title->normalSide,
            ':parent'       => $title->parentId !== null ? UlidGenerator::decode($title->parentId) : null,
            ':sort_order'   => $title->sortOrder,
            ':is_active'    => $title->isActive ? 1 : 0,
            ':created_at'   => $title->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at'   => $title->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE account_titles
                SET deleted_at = :deleted_at, is_active = 0, updated_at = :updated_at
              WHERE id = :id AND deleted_at IS NULL',
        );
        $stmt->execute([
            ':deleted_at' => $deletedAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $deletedAt->format('Y-m-d H:i:s.u'),
            ':id'         => UlidGenerator::decode($id),
        ]);
    }

    public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM account_titles
                 WHERE entity_id = :entity AND code = :code AND deleted_at IS NULL';
        $params = [
            ':entity' => UlidGenerator::decode($entityId),
            ':code'   => $code,
        ];
        if ($excludeId !== null && $excludeId !== '') {
            $sql .= ' AND id <> :exclude';
            $params[':exclude'] = UlidGenerator::decode($excludeId);
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() !== false;
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildFilter(
        string $entityId,
        ?string $category,
        ?bool $isActive,
        ?string $search,
    ): array {
        $clauses = ['entity_id = :entity', 'deleted_at IS NULL'];
        $params = [':entity' => UlidGenerator::decode($entityId)];

        if ($category !== null && $category !== '') {
            $clauses[] = 'category = :category';
            $params[':category'] = $category;
        }
        if ($isActive !== null) {
            $clauses[] = 'is_active = :active';
            $params[':active'] = $isActive ? 1 : 0;
        }
        if ($search !== null && $search !== '') {
            $clauses[] = '(name LIKE :search OR code LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        return [implode(' AND ', $clauses), $params];
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): AccountTitle
    {
        return new AccountTitle(
            id: self::stringifyId($row['id'] ?? ''),
            entityId: self::stringifyId($row['entity_id'] ?? ''),
            code: (string) ($row['code'] ?? ''),
            name: (string) ($row['name'] ?? ''),
            category: (string) ($row['category'] ?? 'asset'),
            normalSide: (string) ($row['normal_side'] ?? 'debit'),
            parentId: isset($row['parent_id']) && is_string($row['parent_id'])
                ? self::stringifyId($row['parent_id'])
                : null,
            sortOrder: (int) ($row['sort_order'] ?? 0),
            isActive: self::toBool($row['is_active'] ?? true),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? new DateTimeImmutable('@0'),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? new DateTimeImmutable('@0'),
        );
    }

    private static function stringifyId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function toBool(mixed $v): bool
    {
        if (is_bool($v)) {
            return $v;
        }
        if (is_int($v)) {
            return $v !== 0;
        }
        if (is_string($v)) {
            return $v !== '' && $v !== '0';
        }
        return (bool) $v;
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
}
