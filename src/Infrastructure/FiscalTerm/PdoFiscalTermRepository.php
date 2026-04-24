<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FiscalTerm;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\FiscalTerm\FiscalTerm;
use Rucaro\Domain\FiscalTerm\FiscalTermRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoFiscalTermRepository implements FiscalTermRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function listByEntity(string $entityId): array
    {
        $sql = 'SELECT id, entity_id, fiscal_period, start_date, end_date,
                       is_closed, closed_at, created_at, updated_at
                FROM fiscal_terms
                WHERE entity_id = :entity
                ORDER BY fiscal_period DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findById(string $id): ?FiscalTerm
    {
        $sql = 'SELECT id, entity_id, fiscal_period, start_date, end_date,
                       is_closed, closed_at, created_at, updated_at
                FROM fiscal_terms
                WHERE id = :id
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function save(FiscalTerm $term): void
    {
        $sql = 'INSERT INTO fiscal_terms
                    (id, entity_id, fiscal_period, start_date, end_date,
                     is_closed, closed_at, created_at, updated_at)
                VALUES
                    (:id, :entity, :period, :start, :end,
                     :is_closed, :closed_at, :created_at, :updated_at)
                ON DUPLICATE KEY UPDATE
                    fiscal_period = VALUES(fiscal_period),
                    start_date = VALUES(start_date),
                    end_date = VALUES(end_date),
                    is_closed = VALUES(is_closed),
                    closed_at = VALUES(closed_at),
                    updated_at = VALUES(updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'         => UlidGenerator::decode($term->id),
            ':entity'     => UlidGenerator::decode($term->entityId),
            ':period'     => $term->fiscalPeriod,
            ':start'      => $term->startDate->format('Y-m-d'),
            ':end'        => $term->endDate->format('Y-m-d'),
            ':is_closed'  => $term->isClosed ? 1 : 0,
            ':closed_at'  => $term->closedAt?->format('Y-m-d H:i:s.u'),
            ':created_at' => $term->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $term->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM fiscal_terms WHERE id = :id');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    public function existsByPeriod(string $entityId, int $fiscalPeriod, ?string $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM fiscal_terms
                 WHERE entity_id = :entity AND fiscal_period = :period';
        $params = [
            ':entity' => UlidGenerator::decode($entityId),
            ':period' => $fiscalPeriod,
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
    private function hydrate(array $row): FiscalTerm
    {
        return new FiscalTerm(
            id: self::stringifyId($row['id'] ?? ''),
            entityId: self::stringifyId($row['entity_id'] ?? ''),
            fiscalPeriod: (int) ($row['fiscal_period'] ?? 0),
            startDate: self::parseDate($row['start_date'] ?? null) ?? new DateTimeImmutable('1970-01-01', new DateTimeZone('UTC')),
            endDate: self::parseDate($row['end_date'] ?? null) ?? new DateTimeImmutable('1970-12-31', new DateTimeZone('UTC')),
            isClosed: self::toBool($row['is_closed'] ?? false),
            closedAt: self::parseTimestamp($row['closed_at'] ?? null),
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

    private static function parseDate(mixed $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '' || !is_string($raw)) {
            return null;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable(substr($raw, 0, 10), new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }
}
