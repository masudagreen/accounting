<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

/**
 * Materialise per-entity `account_titles` rows from the master chart of
 * accounts.
 *
 * F-3: the canonical chart of accounts in the legacy system is the JSON
 * tree stored in `accountingFSJpn.jsonJgaapAccountTitle{BS,PL,CR}` —
 * not the implicit set of `idAccountTitle` values referenced by journals.
 * This importer therefore:
 *
 *   1. For every entity, picks the LATEST `numFiscalPeriod` row and reads
 *      its three JSON trees.
 *   2. Walks each tree, collecting real leaves (subtotal nodes ending in
 *      `Sum` / `Net` are excluded — see {@see AccountTitleClassifier::extractLeavesFromTree}).
 *   3. Falls back to journal-line scanning to pick up any code that is
 *      referenced by journals but missing from the tree (legacy data
 *      hygiene gap). These get the standard expense/debit fallback so
 *      journal FK lookups never break.
 *
 * The new schema constrains `code` to VARCHAR(16) but legacy camelCase
 * identifiers easily exceed that, so we synthesise short `L%04d` codes and
 * preserve the original camelCase via the IdMapping key (legacyEntityId:legacyCode)
 * and as a suffix on `name` for traceability.
 */
final class LegacyAccountTitleImporter
{
    public function __construct(
        private readonly \PDO $source,
        private readonly \PDO $target,
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

        $insert = $this->target->prepare(
            'INSERT INTO account_titles
                 (id, entity_id, code, name, category, normal_side,
                  parent_id, sort_order, is_active)
             VALUES
                 (:id, :ent, :code, :name, :cat, :side,
                  NULL, :sort, :active)',
        );

        foreach ($this->readEntityIds() as $legacyEntityId) {
            $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $legacyEntityId);
            if ($entityBin === null) {
                ++$skipped;
                $notes[] = sprintf(
                    'entity#%d has no mapping; account_titles skipped',
                    $legacyEntityId,
                );
                continue;
            }

            // 1) Tree-derived leaves (master chart of accounts).
            $treeLeaves = $this->readChartFromJsonTree($legacyEntityId);

            // 2) Journal-referenced codes that are NOT in the tree
            //    (legacy data-hygiene fallback).
            $treeCodes = [];
            foreach ($treeLeaves as $row) {
                $treeCodes[$row['code']] = true;
            }
            $journalCodes = $this->readAccountTitleCodes($legacyEntityId);
            $journalOnly = [];
            foreach ($journalCodes as $code) {
                if ($code === '' || $code === 'else') {
                    continue;
                }
                if (isset($treeCodes[$code])) {
                    continue;
                }
                $journalOnly[] = $code;
            }

            $seq = 0;
            foreach ($treeLeaves as $row) {
                ++$read;
                ++$seq;
                if ($this->writeRow(
                    insert: $insert,
                    legacyEntityId: $legacyEntityId,
                    entityBin: $entityBin,
                    legacyCode: $row['code'],
                    label: $row['label'],
                    category: $row['category'],
                    side: $row['normal_side'],
                    seq: $seq,
                    notes: $notes,
                )) {
                    ++$inserted;
                } else {
                    ++$skipped;
                }
            }

            foreach ($journalOnly as $legacyCode) {
                ++$read;
                ++$seq;
                [$category, $side, $label] = AccountTitleClassifier::classify($legacyCode);
                if ($this->writeRow(
                    insert: $insert,
                    legacyEntityId: $legacyEntityId,
                    entityBin: $entityBin,
                    legacyCode: $legacyCode,
                    label: $label,
                    category: $category,
                    side: $side,
                    seq: $seq,
                    notes: $notes,
                )) {
                    ++$inserted;
                } else {
                    ++$skipped;
                }
            }
        }

        return new ImportReport('account_titles', $read, $inserted, $skipped, $notes);
    }

    /**
     * @param list<string> $notes
     */
    private function writeRow(
        \PDOStatement $insert,
        int $legacyEntityId,
        string $entityBin,
        string $legacyCode,
        string $label,
        string $category,
        string $side,
        int $seq,
        array &$notes,
    ): bool {
        $code = LegacyValueConverter::syntheticAccountTitleCode($seq);
        $mapKey = sprintf('%d:%s', $legacyEntityId, $legacyCode);
        $binaryUlid = $this->idMap->getOrCreate(IdMapping::TABLE_ACCOUNT_TITLES, $mapKey);

        if ($this->dryRun) {
            $notes[] = sprintf(
                'DRY: account_title entity#%d %s -> %s (%s)',
                $legacyEntityId,
                $legacyCode,
                $code,
                $label,
            );

            return true;
        }

        $displayName = ($label === '' || $label === $legacyCode)
            ? $legacyCode
            : sprintf('%s (%s)', $label, $legacyCode);

        $insert->bindValue(':id', $binaryUlid, \PDO::PARAM_LOB);
        $insert->bindValue(':ent', $entityBin, \PDO::PARAM_LOB);
        $insert->bindValue(':code', $code);
        $insert->bindValue(':name', $displayName);
        $insert->bindValue(':cat', $category);
        $insert->bindValue(':side', $side);
        $insert->bindValue(':sort', $seq, \PDO::PARAM_INT);
        $insert->bindValue(':active', true, \PDO::PARAM_BOOL);
        $insert->execute();

        return true;
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
            /* @var array<string,mixed> $row */
            $out[] = (int) $row['id'];
        }

        return $out;
    }

    /**
     * Read the master chart-of-accounts JSON trees (BS / PL / CR) for the
     * given entity's MOST RECENT fiscal period and return a flat ordered
     * list of leaves, deduplicated across the three trees.
     *
     * @return list<array{code:string,label:string,category:string,normal_side:string,sort_order:int}>
     */
    private function readChartFromJsonTree(int $entityId): array
    {
        $stmt = $this->source->prepare(
            'SELECT jsonJgaapAccountTitleBS AS bs,
                    jsonJgaapAccountTitlePL AS pl,
                    jsonJgaapAccountTitleCR AS cr
               FROM accountingFSJpn
              WHERE idEntity = :e
              ORDER BY numFiscalPeriod DESC
              LIMIT 1',
        );
        $stmt->execute([':e' => $entityId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return [];
        }

        /** @var array<string, array{code:string,label:string,category:string,normal_side:string,sort_order:int}> $byCode */
        $byCode = [];
        $sort = 0;

        foreach (['bs', 'pl', 'cr'] as $key) {
            $raw = $row[$key] ?? null;
            if ($raw === null || $raw === '' || !is_string($raw)) {
                continue;
            }
            $decoded = json_decode($raw, true);
            if (!is_array($decoded) || $decoded === []) {
                continue;
            }
            /** @var list<array<string, mixed>> $decoded */
            $leaves = AccountTitleClassifier::extractLeavesFromTree($decoded);
            foreach ($leaves as $leaf) {
                if (isset($byCode[$leaf['code']])) {
                    continue;
                }
                ++$sort;
                $leaf['sort_order'] = $sort;
                $byCode[$leaf['code']] = $leaf;
            }
        }

        return array_values($byCode);
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
            /* @var array<string,mixed> $r */
            $out[] = (string) $r['code'];
        }

        return $out;
    }
}
