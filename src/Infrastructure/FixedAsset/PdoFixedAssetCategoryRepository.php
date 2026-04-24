<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FixedAsset;

use PDO;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
use Rucaro\Domain\FixedAsset\FixedAssetCategory;
use Rucaro\Domain\FixedAsset\FixedAssetCategoryRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoFixedAssetCategoryRepository implements FixedAssetCategoryRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM fixed_asset_categories ORDER BY sort_order ASC, code ASC',
        );
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findByCode(string $code): ?FixedAssetCategory
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fixed_asset_categories WHERE code = :c LIMIT 1');
        $stmt->execute([':c' => $code]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): FixedAssetCategory
    {
        $rawId = $row['id'] ?? '';
        $id = is_string($rawId) && strlen($rawId) === 16 ? UlidGenerator::encode($rawId) : (string) $rawId;
        return new FixedAssetCategory(
            id: $id,
            code: (string) ($row['code'] ?? ''),
            label: (string) ($row['label'] ?? ''),
            parentCode: isset($row['parent_code']) && $row['parent_code'] !== null ? (string) $row['parent_code'] : null,
            sortOrder: (int) ($row['sort_order'] ?? 0),
            isTangible: (int) ($row['is_tangible'] ?? 1) === 1,
            isDepreciable: (int) ($row['is_depreciable'] ?? 1) === 1,
            defaultUsefulLifeYears: (int) ($row['default_useful_life_years'] ?? 0),
            defaultMethod: DepreciationMethod::from((string) ($row['default_method'] ?? 'straight_line')),
        );
    }
}
