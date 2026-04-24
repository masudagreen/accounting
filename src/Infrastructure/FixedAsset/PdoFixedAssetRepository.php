<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see FixedAssetRepositoryInterface}.
 *
 * MariaDB layout: `fixed_assets` with BINARY(16) keys. All IDs round-trip
 * through {@see UlidGenerator::decode} / {@see UlidGenerator::encode} so
 * the domain layer sees strings only.
 */
final class PdoFixedAssetRepository implements FixedAssetRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(FixedAsset $asset): void
    {
        $sql = <<<'SQL'
            INSERT INTO fixed_assets (
                id, entity_id, asset_code, asset_name, category_code,
                asset_account_title_id, accumulated_depreciation_account_title_id, depreciation_expense_account_title_id,
                acquisition_date, service_start_date, disposal_date,
                acquisition_cost, residual_value, useful_life_years, method,
                quantity, department_code, note,
                created_by, created_at, updated_at, deleted_at
            ) VALUES (
                :id, :entity, :code, :name, :category,
                :asset_at, :accum_at, :expense_at,
                :acq_date, :svc_date, :disposal_date,
                :cost, :residual, :ul, :method,
                :qty, :dept, :note,
                :created_by, :created_at, :updated_at, :deleted_at
            )
            ON DUPLICATE KEY UPDATE
                asset_name = VALUES(asset_name),
                category_code = VALUES(category_code),
                asset_account_title_id = VALUES(asset_account_title_id),
                accumulated_depreciation_account_title_id = VALUES(accumulated_depreciation_account_title_id),
                depreciation_expense_account_title_id = VALUES(depreciation_expense_account_title_id),
                acquisition_date = VALUES(acquisition_date),
                service_start_date = VALUES(service_start_date),
                disposal_date = VALUES(disposal_date),
                acquisition_cost = VALUES(acquisition_cost),
                residual_value = VALUES(residual_value),
                useful_life_years = VALUES(useful_life_years),
                method = VALUES(method),
                quantity = VALUES(quantity),
                department_code = VALUES(department_code),
                note = VALUES(note),
                updated_at = VALUES(updated_at),
                deleted_at = VALUES(deleted_at)
            SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'         => UlidGenerator::decode($asset->id),
            ':entity'     => UlidGenerator::decode($asset->entityId),
            ':code'       => $asset->assetCode,
            ':name'       => $asset->assetName,
            ':category'   => $asset->categoryCode,
            ':asset_at'   => $asset->assetAccountTitleId !== null ? UlidGenerator::decode($asset->assetAccountTitleId) : null,
            ':accum_at'   => $asset->accumulatedDepreciationAccountTitleId !== null ? UlidGenerator::decode($asset->accumulatedDepreciationAccountTitleId) : null,
            ':expense_at' => $asset->depreciationExpenseAccountTitleId !== null ? UlidGenerator::decode($asset->depreciationExpenseAccountTitleId) : null,
            ':acq_date'   => $asset->acquisitionDate->format('Y-m-d'),
            ':svc_date'   => $asset->serviceStartDate->format('Y-m-d'),
            ':disposal_date' => $asset->disposalDate?->format('Y-m-d'),
            ':cost'       => $asset->acquisitionCost,
            ':residual'   => $asset->residualValue,
            ':ul'         => $asset->usefulLifeYears,
            ':method'     => $asset->method->value,
            ':qty'        => $asset->quantity,
            ':dept'       => $asset->departmentCode,
            ':note'       => $asset->note,
            ':created_by' => UlidGenerator::decode($asset->createdBy),
            ':created_at' => $asset->createdAt->format('Y-m-d H:i:s.u'),
            ':updated_at' => $asset->updatedAt->format('Y-m-d H:i:s.u'),
            ':deleted_at' => $asset->deletedAt?->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function findById(string $id): ?FixedAsset
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fixed_assets WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntityAndCode(string $entityId, string $assetCode): ?FixedAsset
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM fixed_assets WHERE entity_id = :e AND asset_code = :c LIMIT 1',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':c' => $assetCode,
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntity(string $entityId, bool $includeDisposed = false): array
    {
        $sql = 'SELECT * FROM fixed_assets WHERE entity_id = :e AND deleted_at IS NULL';
        if (!$includeDisposed) {
            $sql .= ' AND disposal_date IS NULL';
        }
        $sql .= ' ORDER BY asset_code ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':e' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): FixedAsset
    {
        return new FixedAsset(
            id: self::encodeId($row['id'] ?? ''),
            entityId: self::encodeId($row['entity_id'] ?? ''),
            assetCode: (string) ($row['asset_code'] ?? ''),
            assetName: (string) ($row['asset_name'] ?? ''),
            categoryCode: (string) ($row['category_code'] ?? 'other'),
            assetAccountTitleId: self::encodeIdOrNull($row['asset_account_title_id'] ?? null),
            accumulatedDepreciationAccountTitleId: self::encodeIdOrNull($row['accumulated_depreciation_account_title_id'] ?? null),
            depreciationExpenseAccountTitleId: self::encodeIdOrNull($row['depreciation_expense_account_title_id'] ?? null),
            acquisitionDate: self::parseDate((string) ($row['acquisition_date'] ?? '')),
            serviceStartDate: self::parseDate((string) ($row['service_start_date'] ?? '')),
            disposalDate: self::parseDateOrNull($row['disposal_date'] ?? null),
            acquisitionCost: (string) ($row['acquisition_cost'] ?? '0.0000'),
            residualValue: (string) ($row['residual_value'] ?? '0.0000'),
            usefulLifeYears: (int) ($row['useful_life_years'] ?? 0),
            method: DepreciationMethod::fromDbString((string) ($row['method'] ?? 'straight_line')),
            quantity: (int) ($row['quantity'] ?? 1),
            departmentCode: self::nullableString($row['department_code'] ?? null),
            note: self::nullableString($row['note'] ?? null),
            createdBy: self::encodeId($row['created_by'] ?? ''),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? new DateTimeImmutable('now', new DateTimeZone('UTC')),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? new DateTimeImmutable('now', new DateTimeZone('UTC')),
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

    private static function encodeIdOrNull(mixed $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        if (!is_string($raw) || $raw === '') {
            return null;
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

    private static function parseDate(string $raw): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return new DateTimeImmutable('1970-01-01', new DateTimeZone('UTC'));
        }
    }

    private static function parseDateOrNull(mixed $raw): ?DateTimeImmutable
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
