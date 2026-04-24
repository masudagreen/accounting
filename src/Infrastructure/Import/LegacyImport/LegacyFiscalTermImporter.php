<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;

/**
 * Import legacy `accountingEntityJpn` rows into new `fiscal_terms`.
 *
 * Each (idEntity, numFiscalPeriod) row becomes one `fiscal_terms` record.
 * We intentionally ignore the near-empty legacy `baseTerm` table: it holds
 * unrelated "time bracket" records that were never linked to accounting.
 *
 * Mapping key uses the synthetic `"{entityId}-{numFiscalPeriod}"` string so
 * later journal importers can resolve the new ULID deterministically.
 */
final class LegacyFiscalTermImporter
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
        $rows = $this->source->query(
            'SELECT idEntity, numFiscalPeriod, numFiscalBeginningYear,
                    numFiscalBeginningMonth, numFiscalTermMonth
               FROM accountingEntityJpn
              WHERE numFiscalBeginningYear IS NOT NULL
                AND numFiscalBeginningMonth IS NOT NULL
              ORDER BY idEntity, numFiscalPeriod'
        );
        if ($rows === false) {
            return ImportReport::empty('fiscal_terms', ['source query failed']);
        }

        $read = 0;
        $inserted = 0;
        $skipped = 0;
        /** @var list<string> $notes */
        $notes = [];

        $insert = $this->target->prepare(
            'INSERT INTO fiscal_terms
                 (id, entity_id, fiscal_period, start_date, end_date, is_closed)
             VALUES
                 (:id, :ent, :fp, :sd, :ed, :closed)'
        );

        foreach ($rows as $r) {
            ++$read;
            /** @var array<string,mixed> $r */
            $entityId = (int) $r['idEntity'];
            $period = (int) $r['numFiscalPeriod'];
            $beginYear = (int) $r['numFiscalBeginningYear'];
            $beginMonth = (int) $r['numFiscalBeginningMonth'];
            $termMonths = (int) ($r['numFiscalTermMonth'] ?? 12);
            if ($termMonths <= 0) {
                $termMonths = 12;
            }

            $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $entityId);
            if ($entityBin === null) {
                ++$skipped;
                $notes[] = sprintf(
                    'fiscal_term skipped: entity#%d period#%d (no entity mapping)',
                    $entityId,
                    $period,
                );
                continue;
            }

            $dates = LegacyValueConverter::fiscalTermDates($beginYear, $beginMonth, $termMonths);

            $key = sprintf('%d-%d', $entityId, $period);
            $binaryUlid = $this->idMap->getOrCreate(IdMapping::TABLE_FISCAL_TERMS, $key);

            if ($this->dryRun) {
                ++$inserted;
                $notes[] = sprintf(
                    'DRY: fiscal_term %s [%s..%s]',
                    $key,
                    $dates['start'],
                    $dates['end'],
                );
                continue;
            }

            $insert->bindValue(':id', $binaryUlid, PDO::PARAM_LOB);
            $insert->bindValue(':ent', $entityBin, PDO::PARAM_LOB);
            $insert->bindValue(':fp', $period, PDO::PARAM_INT);
            $insert->bindValue(':sd', $dates['start']);
            $insert->bindValue(':ed', $dates['end']);
            $insert->bindValue(':closed', false, PDO::PARAM_BOOL);
            $insert->execute();
            ++$inserted;
        }

        return new ImportReport('fiscal_terms', $read, $inserted, $skipped, $notes);
    }
}
