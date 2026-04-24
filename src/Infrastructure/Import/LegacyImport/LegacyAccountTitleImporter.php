<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;

/**
 * Create `account_titles` rows for every legacy camelCase code referenced
 * by journal lines.
 *
 * The legacy schema does not have a proper chart-of-accounts master —
 * `accountingAccount` only holds the "currently selected entity" pointer.
 * The universe of account titles is implicit: it is the set of distinct
 * `idAccountTitle` values found in `accountingLogCalcJpn`. We materialise
 * that universe into the new `account_titles` table, scoped per entity.
 *
 * Because the new schema has `code VARCHAR(16)` and legacy codes can be up
 * to 35 chars, we assign synthetic short codes ("L0001", ...) and preserve
 * the original camelCase in the IdMapping key and in `name` as a suffix
 * for traceability. The Japanese label from the classifier becomes the
 * primary display name.
 */
final class LegacyAccountTitleImporter
{
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

        $entities = $this->readEntityIds();

        $insert = $this->target->prepare(
            'INSERT INTO account_titles
                 (id, entity_id, code, name, category, normal_side,
                  parent_id, sort_order, is_active)
             VALUES
                 (:id, :ent, :code, :name, :cat, :side,
                  NULL, :sort, :active)'
        );

        foreach ($entities as $legacyEntityId) {
            $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $legacyEntityId);
            if ($entityBin === null) {
                ++$skipped;
                $notes[] = sprintf('entity#%d has no mapping; account_titles skipped', $legacyEntityId);
                continue;
            }

            $codes = $this->readAccountTitleCodes($legacyEntityId);
            $seq = 1;
            foreach ($codes as $legacyCode) {
                if ($legacyCode === '' || $legacyCode === 'else') {
                    continue;
                }
                ++$read;

                [$category, $side, $label] = AccountTitleClassifier::classify($legacyCode);
                $code = LegacyValueConverter::syntheticAccountTitleCode($seq);
                $mapKey = sprintf('%d:%s', $legacyEntityId, $legacyCode);
                $binaryUlid = $this->idMap->getOrCreate(IdMapping::TABLE_ACCOUNT_TITLES, $mapKey);

                if ($this->dryRun) {
                    ++$inserted;
                    $notes[] = sprintf(
                        'DRY: account_title entity#%d %s -> %s (%s)',
                        $legacyEntityId,
                        $legacyCode,
                        $code,
                        $label,
                    );
                    ++$seq;
                    continue;
                }

                $displayName = $label === $legacyCode
                    ? $legacyCode
                    : sprintf('%s (%s)', $label, $legacyCode);

                $insert->bindValue(':id', $binaryUlid, PDO::PARAM_LOB);
                $insert->bindValue(':ent', $entityBin, PDO::PARAM_LOB);
                $insert->bindValue(':code', $code);
                $insert->bindValue(':name', $displayName);
                $insert->bindValue(':cat', $category);
                $insert->bindValue(':side', $side);
                $insert->bindValue(':sort', $seq, PDO::PARAM_INT);
                $insert->bindValue(':active', true, PDO::PARAM_BOOL);
                $insert->execute();
                ++$inserted;
                ++$seq;
            }
        }

        return new ImportReport('account_titles', $read, $inserted, $skipped, $notes);
    }

    /**
     * @return list<int>
     */
    private function readEntityIds(): array
    {
        $stmt = $this->source->query('SELECT id FROM accountingEntity ORDER BY id');
        if ($stmt === false) {
            return [];
        }
        /** @var list<int> $out */
        $out = [];
        foreach ($stmt as $row) {
            /** @var array<string,mixed> $row */
            $out[] = (int) $row['id'];
        }
        return $out;
    }

    /**
     * @return list<string>
     */
    private function readAccountTitleCodes(int $entityId): array
    {
        $sql = 'SELECT DISTINCT idAccountTitle AS code
                  FROM accountingLogCalcJpn
                 WHERE idEntity = :e
                   AND idAccountTitle IS NOT NULL
                   AND idAccountTitle <> ""
                 ORDER BY idAccountTitle';
        $stmt = $this->source->prepare($sql);
        $stmt->execute([':e' => $entityId]);
        /** @var list<string> $out */
        $out = [];
        foreach ($stmt as $r) {
            /** @var array<string,mixed> $r */
            $out[] = (string) $r['code'];
        }
        return $out;
    }
}
