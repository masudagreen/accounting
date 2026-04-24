<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Entity;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Entity\EntityRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoEntityRepository implements EntityRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function listByOwner(
        string $ownerUserId,
        int $page,
        int $pageSize,
        ?string $search = null,
        ?bool $isActive = null,
    ): array {
        [$where, $params] = $this->buildFilter($ownerUserId, $search, $isActive);
        $offset = ($page - 1) * $pageSize;
        $sql = 'SELECT id, owner_user_id, name, nation_code, currency_code,
                       fiscal_start_mmdd, is_active, is_corporate,
                       created_at, updated_at, deleted_at
                FROM entities
                WHERE ' . $where . '
                ORDER BY created_at DESC, id DESC
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

    public function countByOwner(
        string $ownerUserId,
        ?string $search = null,
        ?bool $isActive = null,
    ): int {
        [$where, $params] = $this->buildFilter($ownerUserId, $search, $isActive);
        $sql = 'SELECT COUNT(*) FROM entities WHERE ' . $where;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        /** @var string|false $c */
        $c = $stmt->fetchColumn();
        return $c === false ? 0 : (int) $c;
    }

    public function findById(string $id): ?Entity
    {
        $sql = 'SELECT id, owner_user_id, name, nation_code, currency_code,
                       fiscal_start_mmdd, is_active, is_corporate,
                       created_at, updated_at, deleted_at
                FROM entities
                WHERE id = :id AND deleted_at IS NULL
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function save(Entity $entity): void
    {
        $sql = 'INSERT INTO entities
                    (id, owner_user_id, name, nation_code, currency_code,
                     fiscal_start_mmdd, is_active, is_corporate,
                     created_at, updated_at)
                VALUES
                    (:id, :owner, :name, :nation, :currency,
                     :mmdd, :is_active, :is_corporate,
                     :created_at, :updated_at)
                ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    nation_code = VALUES(nation_code),
                    currency_code = VALUES(currency_code),
                    fiscal_start_mmdd = VALUES(fiscal_start_mmdd),
                    is_active = VALUES(is_active),
                    is_corporate = VALUES(is_corporate),
                    updated_at = VALUES(updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'           => UlidGenerator::decode($entity->id),
            ':owner'        => UlidGenerator::decode($entity->ownerUserId),
            ':name'         => $entity->name,
            ':nation'       => $entity->nationCode,
            ':currency'     => $entity->currencyCode,
            ':mmdd'         => $entity->fiscalStartMmDd,
            ':is_active'    => $entity->isActive ? 1 : 0,
            ':is_corporate' => $entity->isCorporate ? 1 : 0,
            ':created_at'   => $entity->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at'   => $entity->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE entities
                SET deleted_at = :deleted_at, is_active = 0, updated_at = :updated_at
              WHERE id = :id AND deleted_at IS NULL',
        );
        $stmt->execute([
            ':deleted_at' => $deletedAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $deletedAt->format('Y-m-d H:i:s.u'),
            ':id'         => UlidGenerator::decode($id),
        ]);
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildFilter(string $ownerUserId, ?string $search, ?bool $isActive): array
    {
        $clauses = ['owner_user_id = :owner', 'deleted_at IS NULL'];
        $params = [':owner' => UlidGenerator::decode($ownerUserId)];

        if ($search !== null && $search !== '') {
            $clauses[] = 'name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }
        if ($isActive !== null) {
            $clauses[] = 'is_active = :active';
            $params[':active'] = $isActive ? 1 : 0;
        }
        return [implode(' AND ', $clauses), $params];
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Entity
    {
        return new Entity(
            id: self::stringifyId($row['id'] ?? ''),
            ownerUserId: self::stringifyId($row['owner_user_id'] ?? ''),
            name: (string) ($row['name'] ?? ''),
            nationCode: (string) ($row['nation_code'] ?? 'JPN'),
            currencyCode: (string) ($row['currency_code'] ?? 'JPY'),
            fiscalStartMmDd: (string) ($row['fiscal_start_mmdd'] ?? '0101'),
            isActive: self::toBool($row['is_active'] ?? true),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? new DateTimeImmutable('@0'),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? new DateTimeImmutable('@0'),
            deletedAt: self::parseTimestamp($row['deleted_at'] ?? null),
            isCorporate: self::toBool($row['is_corporate'] ?? true),
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
