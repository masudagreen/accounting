<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;

/**
 * Import legacy `accountingLogFixedAssetsJpn` rows into `fixed_assets`.
 *
 * In the dataset being migrated this table holds zero rows
 * (`accountingFixedAssetsJpn` is only a per-term configuration table — not
 * an asset book). We still expose an importer so running on a richer
 * legacy DB later does not require code changes.
 *
 * Category mapping: legacy stored a free-text category via
 * `flagDepMethod` / `idAccountTitle`; we fall back to the generic
 * `tangible_asset_other` code seeded by 0011_fixed_assets_seed.sql.
 */
final class LegacyFixedAssetImporter
{
    private const DEFAULT_CATEGORY_CODE = 'tangible_asset_other';

    public function __construct(
        private readonly PDO $source,
        private readonly PDO $target,
        private readonly IdMapping $idMap,
        private readonly bool $dryRun,
    ) {
    }

    public function run(): ImportReport
    {
        $read = 0;
        $inserted = 0;
        $skipped = 0;
        /** @var list<string> $notes */
        $notes = [];

        $rows = $this->source->query(
            'SELECT id, idFixedAssets, idEntity, numFiscalPeriod,
                    strTitle, idAccountTitle, flagDepMethod,
                    numUsefulLife, stampBuy, stampStart, numValue,
                    stampRegister, stampUpdate
               FROM accountingLogFixedAssetsJpn
              ORDER BY id'
        );
        if ($rows === false) {
            return ImportReport::empty('fixed_assets', ['source query failed']);
        }

        $createdByStmt = $this->source->query('SELECT id FROM baseAccount ORDER BY id LIMIT 1');
        $createdByBin = null;
        if ($createdByStmt !== false) {
            /** @var array<string,mixed>|false $ub */
            $ub = $createdByStmt->fetch(PDO::FETCH_ASSOC);
            if ($ub !== false) {
                $createdByBin = $this->idMap->lookup(IdMapping::TABLE_USERS, (int) $ub['id']);
            }
        }

        $insert = $this->target->prepare(
            'INSERT INTO fixed_assets
                 (id, entity_id, asset_code, asset_name, category_code,
                  acquisition_date, service_start_date,
                  acquisition_cost, residual_value, useful_life_years,
                  method, quantity, created_by)
             VALUES
                 (:id, :ent, :code, :name, :cat,
                  :adate, :sdate,
                  :cost, :res, :life,
                  :m, 1, :cb)'
        );

        $seq = 1;
        foreach ($rows as $r) {
            ++$read;
            /** @var array<string,mixed> $r */
            $legacyId = (int) $r['id'];
            $entityId = (int) $r['idEntity'];
            $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $entityId);
            if ($entityBin === null || $createdByBin === null) {
                ++$skipped;
                continue;
            }

            $binaryUlid = $this->idMap->getOrCreate(IdMapping::TABLE_FIXED_ASSETS, $legacyId);

            $assetCode = sprintf('FA%04d', $seq);
            $name = (string) ($r['strTitle'] ?? $assetCode);
            $acqStamp = (int) ($r['stampBuy'] ?? $r['stampStart'] ?? 0);
            $srvStamp = (int) ($r['stampStart'] ?? $acqStamp);
            if ($acqStamp <= 0) {
                ++$skipped;
                continue;
            }
            $method = $this->mapMethod((string) ($r['flagDepMethod'] ?? ''));
            $life = (int) ($r['numUsefulLife'] ?? 0);
            if ($life < 1) {
                $life = 1;
            }

            if ($this->dryRun) {
                ++$inserted;
                ++$seq;
                continue;
            }

            $insert->bindValue(':id', $binaryUlid, PDO::PARAM_LOB);
            $insert->bindValue(':ent', $entityBin, PDO::PARAM_LOB);
            $insert->bindValue(':code', $assetCode);
            $insert->bindValue(':name', $name);
            $insert->bindValue(':cat', self::DEFAULT_CATEGORY_CODE);
            $insert->bindValue(':adate', LegacyValueConverter::stampToDate($acqStamp));
            $insert->bindValue(':sdate', LegacyValueConverter::stampToDate($srvStamp));
            $insert->bindValue(':cost', (string) (int) ($r['numValue'] ?? 0));
            $insert->bindValue(':res', '0');
            $insert->bindValue(':life', $life, PDO::PARAM_INT);
            $insert->bindValue(':m', $method);
            $insert->bindValue(':cb', $createdByBin, PDO::PARAM_LOB);
            $insert->execute();
            ++$inserted;
            ++$seq;
        }

        return new ImportReport('fixed_assets', $read, $inserted, $skipped, $notes);
    }

    private function mapMethod(string $legacy): string
    {
        $m = strtolower(trim($legacy));
        return match ($m) {
            'straightline', 'straight', 'teigaku' => 'straight_line',
            'declining', 'teiritsu' => 'declining_balance',
            'oneshot', 'one_shot' => 'one_shot',
            default => 'straight_line',
        };
    }
}
