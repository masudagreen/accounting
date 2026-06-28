<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Materialise per-account 期首繰越 (opening balance) into `opening_balances`.
 *
 * Why this stage exists (Phase B-3c):
 *   - Master holds journals only — there is no per-term opening_balance
 *     table. The renewal `opening_balances` was provisioned by migration
 *     0010 but seeded empty, on the assumption that a future close-fiscal
 *     -term workflow would populate it.
 *   - Importing master's multi-period journals therefore loses everything
 *     before the term's first journal date for balance-sheet accounts.
 *     RC-8 measured a ¥3.9M shortfall on idEntity=1 / 期20 / 現金.
 *
 * Strategy:
 *   For every (entity, period_n, account_title) tuple where the account is
 *   on the balance sheet (asset / liability / equity per
 *   {@see AccountTitleClassifier}), compute
 *
 *       opening = SUM(numValue [* sign per side]) WHERE numFiscalPeriod < n
 *
 *   The sign convention follows the account's normal side so the persisted
 *   `amount` is positive when the account stands in its natural direction.
 *   {@see TrialBalanceRow::compute()} then folds the value in via
 *   `balance = opening + (dr - cr)` (or `opening + (cr - dr)`) without
 *   needing to know the sign convention.
 *
 * Skipped:
 *   - PL accounts (revenue / expense): per-account opening always 0;
 *     prior-year P&L flows into 利益剰余金 at term close, not into the
 *     same account.
 *   - The earliest period for any (entity, account) tuple: by definition
 *     no prior data exists.
 *   - Net-zero results: omitting them keeps the table compact and matches
 *     `ZeroOpeningBalanceRepository`'s implicit semantics.
 *
 * Idempotent: a re-run uses the same ULID for an existing
 * (entity, term, account) triple via `legacy_id_mapping` and overwrites
 * the `amount` so partial earlier runs converge to the latest aggregation.
 */
final class LegacyOpeningBalanceImporter
{
    /**
     * Mapping key prefix in {@see IdMapping} for opening_balances rows.
     * Composite key: "{entityId}:{period}:{legacyAccountCode}".
     */
    public const TABLE_OPENING_BALANCES = 'opening_balances';

    /**
     * The {@see UlidGenerator} parameter is accepted (and silently ignored)
     * to keep the constructor signature aligned with sibling importers so
     * {@see ImportOrchestrator}'s match() does not need a special case. ULID
     * minting itself is delegated to {@see IdMapping::getOrCreate()}.
     */
    public function __construct(
        private readonly \PDO $source,
        private readonly \PDO $target,
        private readonly IdMapping $idMap,
        UlidGenerator $ulids,
        private readonly bool $dryRun,
    ) {
        unset($ulids);
    }

    public function run(): ImportReport
    {
        $read = 0;
        $inserted = 0;
        $skipped = 0;
        /** @var list<string> $notes */
        $notes = [];

        // Step 1: discover the (entity, period) universe in chronological
        // order so we can compute "prior periods" for each later one.
        $tuples = $this->discoverEntityPeriodTuples();
        if ($tuples === []) {
            return ImportReport::empty(self::TABLE_OPENING_BALANCES, ['no source rows found']);
        }

        // Step 2: index periods per entity to know "what counts as prior".
        /** @var array<int, list<int>> $periodsByEntity */
        $periodsByEntity = [];
        foreach ($tuples as [$entity, $period]) {
            $periodsByEntity[$entity][] = $period;
        }
        foreach ($periodsByEntity as &$ps) {
            $ps = array_values(array_unique($ps));
            sort($ps);
        }
        unset($ps);

        // Step 3: prepared aggregator. Sums numValue with sign by debit flag.
        $aggregate = $this->source->prepare(
            'SELECT idAccountTitle AS code,
                    SUM(CASE WHEN flagDebit = 1 THEN numValue ELSE 0 END) AS dr,
                    SUM(CASE WHEN flagDebit = 0 THEN numValue ELSE 0 END) AS cr
               FROM accountingLogCalcJpn
              WHERE idEntity = :ent
                AND numFiscalPeriod < :period
                AND idAccountTitle IS NOT NULL
                AND idAccountTitle <> ""
                AND idAccountTitle <> "else"
              GROUP BY idAccountTitle',
        );

        // Step 4: prepared upsert (portable across MariaDB + sqlite via
        // explicit existence check, mirroring `IdMapping::persist()`).
        $insertOpening = $this->target->prepare(
            'INSERT INTO opening_balances
                 (id, entity_id, fiscal_term_id, account_title_id, amount, currency_code)
             VALUES
                 (:id, :ent, :term, :at, :amt, :cur)',
        );
        $updateOpening = $this->target->prepare(
            'UPDATE opening_balances
                SET amount = :amt
              WHERE entity_id = :ent
                AND fiscal_term_id = :term
                AND account_title_id = :at',
        );

        foreach ($periodsByEntity as $entity => $periods) {
            $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $entity);
            if ($entityBin === null) {
                ++$skipped;
                $notes[] = sprintf('entity#%d unmapped; skipped', $entity);
                continue;
            }
            foreach ($periods as $period) {
                // First period for this entity → no prior data.
                if ($period === $periods[0]) {
                    continue;
                }

                $termBin = $this->idMap->lookup(
                    IdMapping::TABLE_FISCAL_TERMS,
                    sprintf('%d-%d', $entity, $period),
                );
                if ($termBin === null) {
                    ++$skipped;
                    $notes[] = sprintf(
                        'fiscal_term unmapped: entity#%d period#%d; skipped',
                        $entity,
                        $period,
                    );
                    continue;
                }

                $aggregate->execute([':ent' => $entity, ':period' => $period]);
                /** @var list<array<string,mixed>> $rows */
                $rows = $aggregate->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                foreach ($rows as $r) {
                    ++$read;
                    $code = (string) ($r['code'] ?? '');
                    if ($code === '' || $code === 'else') {
                        ++$skipped;
                        continue;
                    }
                    [$category, $normalSide] = AccountTitleClassifier::classify($code);
                    if (!self::isBalanceSheet($category)) {
                        ++$skipped;
                        continue;
                    }

                    $dr = (int) ($r['dr'] ?? 0);
                    $cr = (int) ($r['cr'] ?? 0);
                    $amount = self::signedAmount($dr, $cr, $normalSide);
                    if ($amount === 0) {
                        ++$skipped;
                        continue;
                    }

                    $atBin = $this->idMap->lookup(
                        IdMapping::TABLE_ACCOUNT_TITLES,
                        sprintf('%d:%s', $entity, $code),
                    );
                    if ($atBin === null) {
                        ++$skipped;
                        $notes[] = sprintf(
                            'account_title unmapped: entity#%d %s; skipped',
                            $entity,
                            $code,
                        );
                        continue;
                    }

                    $amountStr = self::formatScale4($amount);

                    if ($this->dryRun) {
                        ++$inserted;
                        continue;
                    }

                    // Idempotent upsert via legacy_id_mapping ULID.
                    $key = sprintf('%d:%d:%s', $entity, $period, $code);
                    $existed = $this->idMap->lookup(self::TABLE_OPENING_BALANCES, $key) !== null;
                    $obBin = $this->idMap->getOrCreate(self::TABLE_OPENING_BALANCES, $key);

                    if ($existed) {
                        $updateOpening->bindValue(':ent', $entityBin, \PDO::PARAM_LOB);
                        $updateOpening->bindValue(':term', $termBin, \PDO::PARAM_LOB);
                        $updateOpening->bindValue(':at', $atBin, \PDO::PARAM_LOB);
                        $updateOpening->bindValue(':amt', $amountStr);
                        $updateOpening->execute();
                    } else {
                        $insertOpening->bindValue(':id', $obBin, \PDO::PARAM_LOB);
                        $insertOpening->bindValue(':ent', $entityBin, \PDO::PARAM_LOB);
                        $insertOpening->bindValue(':term', $termBin, \PDO::PARAM_LOB);
                        $insertOpening->bindValue(':at', $atBin, \PDO::PARAM_LOB);
                        $insertOpening->bindValue(':amt', $amountStr);
                        $insertOpening->bindValue(':cur', 'JPY');
                        $insertOpening->execute();
                    }
                    ++$inserted;
                }
            }
        }

        $notes[] = sprintf('opening_balances written: %d', $inserted);

        return new ImportReport(self::TABLE_OPENING_BALANCES, $read, $inserted, $skipped, $notes);
    }

    /**
     * @return list<array{0:int,1:int}>
     */
    private function discoverEntityPeriodTuples(): array
    {
        $stmt = $this->source->query(
            'SELECT DISTINCT idEntity AS e, numFiscalPeriod AS p
               FROM accountingLogCalcJpn
              WHERE idEntity IS NOT NULL
                AND numFiscalPeriod IS NOT NULL
              ORDER BY idEntity, numFiscalPeriod',
        );
        if ($stmt === false) {
            return [];
        }
        /** @var list<array{0:int,1:int}> $out */
        $out = [];
        foreach ($stmt as $r) {
            /* @var array<string,mixed> $r */
            $out[] = [(int) $r['e'], (int) $r['p']];
        }

        return $out;
    }

    private static function isBalanceSheet(string $category): bool
    {
        return $category === 'asset' || $category === 'liability' || $category === 'equity';
    }

    /**
     * Convert raw debit / credit totals into a single signed amount that
     * follows the account's normal side: positive = balance is on the
     * natural side, negative = contra (rare but possible).
     */
    private static function signedAmount(int $dr, int $cr, string $normalSide): int
    {
        return $normalSide === 'credit' ? ($cr - $dr) : ($dr - $cr);
    }

    private static function formatScale4(int $amount): string
    {
        // DECIMAL(18,4) — match the column scale exactly so MariaDB does not
        // pad/truncate on insert. `%d` already preserves sign for negatives.
        return sprintf('%d.0000', $amount);
    }
}
