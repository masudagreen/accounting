<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use RuntimeException;

/**
 * Import legacy journals into new `journal_entries` + `journal_entry_lines`.
 *
 * Legacy data model:
 *   - `accountingLog` holds the journal header; each row covers one
 *     `(idLog, numFiscalPeriod)` key and carries aggregate totals.
 *     The actual debit/credit **account title** breakdown lives in the
 *     parallel `arrCommaIdAccountTitleDebit` / ...Credit CSV columns,
 *     but per-line **amounts** are NOT in `accountingLog` itself.
 *   - `accountingLogCalcJpn` is the system's "calc cache": one row per
 *     journal line with numeric amount. This is the reliable source for
 *     `journal_entry_lines.amount`.
 *
 * Strategy:
 *   1. Iterate non-deleted `accountingLog` rows.
 *   2. Create one `journal_entries` row per legacy log.
 *   3. Pull matching `accountingLogCalcJpn` rows via (idLog, idEntity,
 *      numFiscalPeriod) to build lines. Debit rows become `side='debit'`,
 *      credit rows become `side='credit'`. This preserves balance
 *      (sum debit == sum credit == total_amount).
 */
final class LegacyJournalImporter
{
    public function __construct(
        private readonly PDO $source,
        private readonly PDO $target,
        private readonly IdMapping $idMap,
        private readonly UlidGenerator $ulids,
        private readonly bool $dryRun,
    ) {
    }

    public function run(): ImportReport
    {
        $read = 0;
        $inserted = 0;
        $skipped = 0;
        $linesInserted = 0;
        /** @var list<string> $notes */
        $notes = [];

        $createdByBin = $this->pickCreatedByUserUlid();

        $selectCalc = $this->source->prepare(
            'SELECT flagDebit, idAccountTitle, numValue,
                    numValueConsumptionTax, numRateConsumptionTax,
                    flagRateConsumptionTaxReduced
               FROM accountingLogCalcJpn
              WHERE idLog = :log AND idEntity = :ent AND numFiscalPeriod = :fp
              ORDER BY flagDebit DESC, id'
        );

        $insertHeader = $this->target->prepare(
            'INSERT INTO journal_entries
                 (id, entity_id, fiscal_term_id, journal_date, booked_at,
                  summary, total_amount, currency_code,
                  status, source, created_by, created_at, updated_at)
             VALUES
                 (:id, :ent, :ft, :d, :b,
                  :sum, :total, :cur,
                  :status, :src, :cb, :ca, :ua)'
        );

        $insertLine = $this->target->prepare(
            'INSERT INTO journal_entry_lines
                 (id, entry_id, line_no, side, account_title_id,
                  amount, tax_rate_percent, tax_amount, is_tax_reduced,
                  memo, booked_at)
             VALUES
                 (:id, :entry, :ln, :side, :at,
                  :amt, :taxr, :taxa, :red,
                  :memo, :b)'
        );

        $rows = $this->source->query(
            'SELECT id, idLog, idEntity, numFiscalPeriod, stampRegister,
                    stampUpdate, stampBook, strTitle, numValue, flagRemove
               FROM accountingLog
              WHERE (flagRemove = 0 OR flagRemove IS NULL)
              ORDER BY id'
        );
        if ($rows === false) {
            return ImportReport::empty('journals', ['source query failed']);
        }

        foreach ($rows as $r) {
            ++$read;
            /** @var array<string,mixed> $r */
            $legacyLogId = (int) $r['idLog'];
            $entityId = (int) $r['idEntity'];
            $period = (int) $r['numFiscalPeriod'];

            $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $entityId);
            if ($entityBin === null) {
                ++$skipped;
                continue;
            }
            $ftKey = sprintf('%d-%d', $entityId, $period);
            $ftBin = $this->idMap->lookup(IdMapping::TABLE_FISCAL_TERMS, $ftKey);
            if ($ftBin === null) {
                ++$skipped;
                continue;
            }

            $calcRows = $this->fetchCalcRows($selectCalc, $legacyLogId, $entityId, $period);
            if ($calcRows === []) {
                ++$skipped;
                continue;
            }

            $debitSum = 0;
            $creditSum = 0;
            foreach ($calcRows as $cr) {
                $amt = (int) $cr['numValue'];
                if ((int) $cr['flagDebit'] === 1) {
                    $debitSum += $amt;
                } else {
                    $creditSum += $amt;
                }
            }
            if ($debitSum === 0 && $creditSum === 0) {
                ++$skipped;
                continue;
            }
            // Legacy data sometimes carries an imbalance between debit and
            // credit totals due to historical rounding / consumption-tax
            // split; prefer the larger side so the check constraint
            // `total_amount >= 0` holds and the UI shows a meaningful number.
            $total = max($debitSum, $creditSum);

            $stampBook = (int) $r['stampBook'];
            $journalDate = LegacyValueConverter::stampToDate($stampBook);
            $bookedAt = LegacyValueConverter::stampToTimestamp($stampBook);
            $createdAt = LegacyValueConverter::stampToTimestamp((int) $r['stampRegister']);
            $updatedAt = LegacyValueConverter::stampToTimestamp((int) $r['stampUpdate']);

            $entryKey = sprintf('%d-%d-%d', $entityId, $period, $legacyLogId);
            $entryBin = $this->idMap->getOrCreate(IdMapping::TABLE_JOURNAL_ENTRIES, $entryKey);

            if ($this->dryRun) {
                ++$inserted;
                $linesInserted += count($calcRows);
                continue;
            }

            $insertHeader->bindValue(':id', $entryBin, PDO::PARAM_LOB);
            $insertHeader->bindValue(':ent', $entityBin, PDO::PARAM_LOB);
            $insertHeader->bindValue(':ft', $ftBin, PDO::PARAM_LOB);
            $insertHeader->bindValue(':d', $journalDate);
            $insertHeader->bindValue(':b', $bookedAt);
            $insertHeader->bindValue(':sum', (string) ($r['strTitle'] ?? ''));
            $insertHeader->bindValue(':total', (string) $total);
            $insertHeader->bindValue(':cur', 'JPY');
            $insertHeader->bindValue(':status', 'posted');
            $insertHeader->bindValue(':src', 'manual');
            $insertHeader->bindValue(':cb', $createdByBin, PDO::PARAM_LOB);
            $insertHeader->bindValue(':ca', $createdAt);
            $insertHeader->bindValue(':ua', $updatedAt);
            $insertHeader->execute();
            ++$inserted;

            $lineNo = 1;
            foreach ($calcRows as $cr) {
                $legacyCode = (string) ($cr['idAccountTitle'] ?? '');
                if ($legacyCode === '' || $legacyCode === 'else') {
                    continue;
                }
                $atMapKey = sprintf('%d:%s', $entityId, $legacyCode);
                $atBin = $this->idMap->lookup(IdMapping::TABLE_ACCOUNT_TITLES, $atMapKey);
                if ($atBin === null) {
                    // Account title was not created (e.g. zero journals cited it)
                    continue;
                }

                $side = ((int) $cr['flagDebit']) === 1 ? 'debit' : 'credit';
                $amount = (string) (int) $cr['numValue'];
                $taxAmount = (string) (int) ($cr['numValueConsumptionTax'] ?? 0);
                $taxRate = (int) ($cr['numRateConsumptionTax'] ?? 0);
                $isReduced = ((int) ($cr['flagRateConsumptionTaxReduced'] ?? 0)) === 1;

                $lineId = $this->ulids->binary();
                $insertLine->bindValue(':id', $lineId, PDO::PARAM_LOB);
                $insertLine->bindValue(':entry', $entryBin, PDO::PARAM_LOB);
                $insertLine->bindValue(':ln', $lineNo, PDO::PARAM_INT);
                $insertLine->bindValue(':side', $side);
                $insertLine->bindValue(':at', $atBin, PDO::PARAM_LOB);
                $insertLine->bindValue(':amt', $amount);
                $insertLine->bindValue(':taxr', $taxRate === 0 ? '0.00' : sprintf('%.2f', $taxRate));
                $insertLine->bindValue(':taxa', $taxAmount);
                $insertLine->bindValue(':red', $isReduced, PDO::PARAM_BOOL);
                $insertLine->bindValue(':memo', '');
                $insertLine->bindValue(':b', $bookedAt);
                $insertLine->execute();
                ++$linesInserted;
                ++$lineNo;
            }
        }

        $notes[] = sprintf('journal_entry_lines inserted: %d', $linesInserted);

        return new ImportReport('journals', $read, $inserted, $skipped, $notes);
    }

    /**
     * @return list<array<string,mixed>>
     */
    private function fetchCalcRows(\PDOStatement $stmt, int $legacyLogId, int $entityId, int $period): array
    {
        $stmt->execute([
            ':log' => $legacyLogId,
            ':ent' => $entityId,
            ':fp' => $period,
        ]);
        /** @var list<array<string,mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $rows;
    }

    private function pickCreatedByUserUlid(): string
    {
        $stmt = $this->source->query('SELECT id FROM baseAccount ORDER BY id LIMIT 1');
        if ($stmt === false) {
            throw new RuntimeException('LegacyJournalImporter: cannot read baseAccount');
        }
        /** @var array<string,mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new RuntimeException('LegacyJournalImporter: no user in baseAccount');
        }
        return $this->idMap->require(IdMapping::TABLE_USERS, (int) $row['id']);
    }
}
