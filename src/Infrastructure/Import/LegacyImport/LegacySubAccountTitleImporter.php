<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;

/**
 * Import legacy `accountingSubAccountTitleJpn` into `sub_account_titles`.
 *
 * In the dataset we are migrating the legacy table is empty (0 rows), but
 * we still implement the importer for completeness so running the CLI on
 * a richer legacy DB works without code changes.
 */
final class LegacySubAccountTitleImporter
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

        $rows = $this->source->query(
            'SELECT id, idSubAccountTitle, idEntity, numFiscalPeriod,
                    idAccountTitle, strTitle
               FROM accountingSubAccountTitleJpn
              ORDER BY id'
        );
        if ($rows === false) {
            return ImportReport::empty('sub_accounts', ['source query failed']);
        }

        $insert = $this->target->prepare(
            'INSERT INTO sub_account_titles
                 (id, account_title_id, code, name, sort_order, is_active)
             VALUES
                 (:id, :at, :code, :name, :sort, :active)'
        );

        $seq = 1;
        foreach ($rows as $r) {
            ++$read;
            /** @var array<string,mixed> $r */
            $legacyId = (int) $r['id'];
            $legacyAtCode = (string) ($r['idAccountTitle'] ?? '');
            $entityId = (int) $r['idEntity'];
            if ($legacyAtCode === '') {
                ++$skipped;
                continue;
            }

            $atKey = sprintf('%d:%s', $entityId, $legacyAtCode);
            $atBin = $this->idMap->lookup(IdMapping::TABLE_ACCOUNT_TITLES, $atKey);
            if ($atBin === null) {
                ++$skipped;
                $notes[] = sprintf('sub#%d skipped: parent account_title not mapped', $legacyId);
                continue;
            }

            $binaryUlid = $this->idMap->getOrCreate(
                IdMapping::TABLE_SUB_ACCOUNT_TITLES,
                $legacyId,
            );
            $code = sprintf('S%04d', $seq);
            $name = (string) ($r['strTitle'] ?? $code);

            if ($this->dryRun) {
                ++$inserted;
                ++$seq;
                continue;
            }

            $insert->bindValue(':id', $binaryUlid, PDO::PARAM_LOB);
            $insert->bindValue(':at', $atBin, PDO::PARAM_LOB);
            $insert->bindValue(':code', $code);
            $insert->bindValue(':name', $name);
            $insert->bindValue(':sort', $seq, PDO::PARAM_INT);
            $insert->bindValue(':active', true, PDO::PARAM_BOOL);
            $insert->execute();
            ++$inserted;
            ++$seq;
        }

        return new ImportReport('sub_accounts', $read, $inserted, $skipped, $notes);
    }
}
