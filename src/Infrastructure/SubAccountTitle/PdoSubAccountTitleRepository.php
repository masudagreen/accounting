<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\SubAccountTitle;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\SubAccountTitle\SubAccountTitle;
use Rucaro\Domain\SubAccountTitle\SubAccountTitleRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoSubAccountTitleRepository implements SubAccountTitleRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function listByAccountTitle(string $accountTitleId): array
    {
        $sql = 'SELECT id, account_title_id, code, name, sort_order, is_active,
                       created_at, updated_at
                FROM sub_account_titles
                WHERE account_title_id = :parent AND deleted_at IS NULL
                ORDER BY sort_order ASC, code ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':parent' => UlidGenerator::decode($accountTitleId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    public function listByEntity(string $entityId): array
    {
        $sql = 'SELECT s.id, s.account_title_id, s.code, s.name, s.sort_order,
                       s.is_active, s.created_at, s.updated_at
                FROM sub_account_titles s
                INNER JOIN account_titles a ON a.id = s.account_title_id
                WHERE a.entity_id = :entity
                  AND s.deleted_at IS NULL
                  AND a.deleted_at IS NULL
                ORDER BY a.sort_order ASC, a.code ASC, s.sort_order ASC, s.code ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findById(string $id): ?SubAccountTitle
    {
        $sql = 'SELECT id, account_title_id, code, name, sort_order, is_active,
                       created_at, updated_at
                FROM sub_account_titles
                WHERE id = :id AND deleted_at IS NULL
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function save(SubAccountTitle $sub): void
    {
        $sql = 'INSERT INTO sub_account_titles
                    (id, account_title_id, code, name, sort_order, is_active,
                     created_at, updated_at)
                VALUES
                    (:id, :parent, :code, :name, :sort_order, :is_active,
                     :created_at, :updated_at)
                ON DUPLICATE KEY UPDATE
                    code = VALUES(code),
                    name = VALUES(name),
                    sort_order = VALUES(sort_order),
                    is_active = VALUES(is_active),
                    updated_at = VALUES(updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'         => UlidGenerator::decode($sub->id),
            ':parent'     => UlidGenerator::decode($sub->accountTitleId),
            ':code'       => $sub->code,
            ':name'       => $sub->name,
            ':sort_order' => $sub->sortOrder,
            ':is_active'  => $sub->isActive ? 1 : 0,
            ':created_at' => $sub->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $sub->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE sub_account_titles
                SET deleted_at = :deleted_at, is_active = 0, updated_at = :updated_at
              WHERE id = :id AND deleted_at IS NULL',
        );
        $stmt->execute([
            ':deleted_at' => $deletedAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $deletedAt->format('Y-m-d H:i:s.u'),
            ':id'         => UlidGenerator::decode($id),
        ]);
    }

    public function existsByCode(string $accountTitleId, string $code, ?string $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM sub_account_titles
                 WHERE account_title_id = :parent AND code = :code AND deleted_at IS NULL';
        $params = [
            ':parent' => UlidGenerator::decode($accountTitleId),
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
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): SubAccountTitle
    {
        return new SubAccountTitle(
            id: self::stringifyId($row['id'] ?? ''),
            accountTitleId: self::stringifyId($row['account_title_id'] ?? ''),
            code: (string) ($row['code'] ?? ''),
            name: (string) ($row['name'] ?? ''),
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
