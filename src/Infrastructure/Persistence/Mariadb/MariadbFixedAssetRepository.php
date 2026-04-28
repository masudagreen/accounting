<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mariadb;

use App\Domain\Depreciation\Acquisition;
use App\Domain\FixedAssets\DepreciationMethodChoice;
use App\Domain\FixedAssets\FixedAsset;
use App\Domain\FixedAssets\FixedAssetAccountMapping;
use App\Domain\Money\Money;
use App\Infrastructure\Persistence\FixedAssetRepository;
use PDO;

/**
 * accountingLogFixedAssetsJpn テーブルを読む MariaDB 実装.
 */
final class MariadbFixedAssetRepository implements FixedAssetRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @return list<FixedAsset>
     */
    public function findByEntity(int $idEntity): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT idLog, strName, numValue, numUsefulLife, stampStart,
                    numRatioOperate, strMethod,
                    idAccountTitleDepreciation, idAccountTitleAccumulated
             FROM accountingLogFixedAssetsJpn
             WHERE idEntity = :idEntity
               AND (flagRemove IS NULL OR flagRemove = 0)
             ORDER BY idLog ASC',
        );
        $stmt->execute([':idEntity' => $idEntity]);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $assets = [];
        foreach ($rows as $row) {
            $asset = $this->convertRow($row);
            if ($asset !== null) {
                $assets[] = $asset;
            }
        }

        return $assets;
    }

    public function findById(string $id): ?FixedAsset
    {
        $stmt = $this->pdo->prepare(
            'SELECT idLog, strName, numValue, numUsefulLife, stampStart,
                    numRatioOperate, strMethod,
                    idAccountTitleDepreciation, idAccountTitleAccumulated
             FROM accountingLogFixedAssetsJpn
             WHERE idLog = :id
               AND (flagRemove IS NULL OR flagRemove = 0)
             LIMIT 1',
        );
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false || $row === null) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return $this->convertRow($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function convertRow(array $row): ?FixedAsset
    {
        $id   = isset($row['idLog']) && $row['idLog'] !== '' ? (string) $row['idLog'] : null;
        $name = isset($row['strName']) && $row['strName'] !== '' ? (string) $row['strName'] : null;

        if ($id === null || $name === null) {
            return null;
        }

        $cost            = Money::ofYen((int) ($row['numValue'] ?? 0));
        $usefulLifeYears = max(1, (int) ($row['numUsefulLife'] ?? 1));
        $stampStart      = isset($row['stampStart']) && (int) $row['stampStart'] > 0
            ? new \DateTimeImmutable('@' . (int) $row['stampStart'])
            : new \DateTimeImmutable('1970-01-01');

        $businessUseRatioPercent = isset($row['numRatioOperate'])
            ? min(100, max(0, (int) $row['numRatioOperate']))
            : 100;

        $acquisition = new Acquisition(
            cost: $cost,
            usefulLifeYears: $usefulLifeYears,
            acquisitionDate: $stampStart,
            businessUseRatioPercent: $businessUseRatioPercent,
        );

        $method  = $this->resolveMethod((string) ($row['strMethod'] ?? ''));
        $mapping = new FixedAssetAccountMapping(
            depreciationExpenseAccountTitleId: (string) ($row['idAccountTitleDepreciation'] ?? 'depreciation'),
            accumulatedDepreciationAccountTitleId: (string) ($row['idAccountTitleAccumulated'] ?? 'accumulatedDepreciation'),
        );

        try {
            return new FixedAsset($id, $name, $acquisition, $method, $mapping);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    private function resolveMethod(string $raw): DepreciationMethodChoice
    {
        return match ($raw) {
            'declining200'     => DepreciationMethodChoice::Declining200,
            'declining250'     => DepreciationMethodChoice::Declining250,
            'sum_of_years'     => DepreciationMethodChoice::SumOfYears,
            'average'          => DepreciationMethodChoice::Average,
            'voluntary'        => DepreciationMethodChoice::Voluntary,
            'lump_sum_three_year' => DepreciationMethodChoice::LumpSumThreeYear,
            default            => DepreciationMethodChoice::Straight,
        };
    }
}
