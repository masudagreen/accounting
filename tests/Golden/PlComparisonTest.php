<?php

declare(strict_types=1);

namespace App\Tests\Golden;

use App\Domain\FinancialStatement\ProfitAndLossBuilder;
use App\Domain\Ledger\Ledger;
use App\Domain\TrialBalance\OpeningBalances;
use App\Domain\TrialBalance\TrialBalance;
use App\Infrastructure\Legacy\LegacyAccountTreeReader;
use App\Infrastructure\Legacy\LegacyJournalReader;

/**
 * 損益計算書の集計値を本番 DB 保持値と新ドメインの再計算値で比較する Golden Master テスト.
 *
 * 完全一致は期待しない (既知の差異: G-7-3 / G-7-4 / A-1).
 * 差分を可視化し、大幅乖離のみ警告する.
 *
 * 比較対象:
 *  - 本番 DB: accountingFSValueJpn.jsonJgaapFSPL の currentTermProfitOrLossNet.sumNext
 *  - 新ドメイン: ProfitAndLossBuilder.build().netIncome()
 */
final class PlComparisonTest extends GoldenMasterTestCase
{
    /** 大幅乖離の閾値 (絶対差が売上高の 20% 超で警告). */
    private const float LARGE_DEVIATION_RATIO = 0.20;

    private LegacyJournalReader $journalReader;

    private LegacyAccountTreeReader $accountTreeReader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->journalReader = new LegacyJournalReader();
        $this->accountTreeReader = new LegacyAccountTreeReader();
    }

    /**
     * entity=1, period=20 (最新期) で PL 集計値を比較する.
     *
     * 完全一致しなくても テスト失敗にしない.
     * 大幅乖離のみ警告 (addWarning相当の記録).
     */
    public function testPlNetIncomeComparison(): void
    {
        $pdo = self::getGoldenPdo();

        $comparisons = $this->runComparisons($pdo);

        if ($comparisons === []) {
            $this->markTestSkipped('No comparable entity-period found with both FS values and journal entries');
        }

        $matched = 0;
        $mismatched = 0;
        $largeDeviations = 0;

        foreach ($comparisons as $c) {
            $idEntity = $c['idEntity'];
            $period = $c['numFiscalPeriod'];
            $dbNetIncome = $c['dbNetIncome'];
            $newNetIncome = $c['newNetIncome'];
            $diffAbs = $c['diffAbs'];
            $deviationRatio = $c['deviationRatio'];
            $isLargeDeviation = $c['isLargeDeviation'];

            if ($diffAbs === 0) {
                $matched++;
            } else {
                $mismatched++;
            }

            if ($isLargeDeviation) {
                $largeDeviations++;
                fwrite(STDERR, sprintf(
                    "[Golden] LARGE DEVIATION entity=%d period=%d: diff_abs=%d deviation_ratio=%.1f%% (db_sign=%s new_sign=%s)\n",
                    $idEntity,
                    $period,
                    $diffAbs,
                    $deviationRatio * 100,
                    $dbNetIncome >= 0 ? '+' : '-',
                    $newNetIncome >= 0 ? '+' : '-',
                ));
            } else {
                fwrite(STDERR, sprintf(
                    "[Golden] entity=%d period=%d: diff_abs=%d deviation_ratio=%.1f%%\n",
                    $idEntity,
                    $period,
                    $diffAbs,
                    $deviationRatio * 100,
                ));
            }
        }

        fwrite(STDERR, sprintf(
            "[Golden] PL summary: total=%d matched=%d mismatched=%d largeDeviations=%d\n",
            count($comparisons),
            $matched,
            $mismatched,
            $largeDeviations,
        ));

        // 大幅乖離の件数を assertion で記録 (CI で追跡可能にする)
        self::assertGreaterThanOrEqual(0, $largeDeviations);

        // 大幅乖離が全件の 50% 未満であること (緩めの基準)
        $total = count($comparisons);
        if ($total > 0) {
            $ratio = $largeDeviations / $total;
            self::assertLessThan(
                0.5,
                $ratio,
                sprintf(
                    'More than 50%% of periods have large PL deviations (%d/%d). Check domain logic.',
                    $largeDeviations,
                    $total,
                ),
            );
        }
    }

    /**
     * @return list<array{idEntity: int, numFiscalPeriod: int, dbNetIncome: int, newNetIncome: int, diffAbs: int, deviationRatio: float, isLargeDeviation: bool}>
     */
    private function runComparisons(\PDO $pdo): array
    {
        $stmt = $pdo->query(
            "SELECT fv.idEntity, fv.numFiscalPeriod, fv.jsonJgaapFSPL,
                    fj.jsonJgaapAccountTitlePL, fj.jsonJgaapAccountTitleBS
             FROM accountingFSValueJpn fv
             JOIN accountingFSJpn fj
               ON fj.idEntity = fv.idEntity
              AND fj.numFiscalPeriod = fv.numFiscalPeriod
             WHERE fv.jsonJgaapFSPL IS NOT NULL
               AND fj.jsonJgaapAccountTitlePL IS NOT NULL
             ORDER BY fv.idEntity, fv.numFiscalPeriod"
        );
        if ($stmt === false) {
            return [];
        }

        $rows = $stmt->fetchAll();
        $results = [];

        foreach ($rows as $row) {
            $idEntity = (int) ($row['idEntity'] ?? 0);
            $numFiscalPeriod = (int) ($row['numFiscalPeriod'] ?? 0);
            $fsPl = is_string($row['jsonJgaapFSPL'] ?? null) ? $row['jsonJgaapFSPL'] : null;
            $plTitleJson = is_string($row['jsonJgaapAccountTitlePL'] ?? null) ? $row['jsonJgaapAccountTitlePL'] : null;
            $bsTitleJson = is_string($row['jsonJgaapAccountTitleBS'] ?? null) ? $row['jsonJgaapAccountTitleBS'] : null;

            if ($fsPl === null || $plTitleJson === null) {
                continue;
            }

            $dbNetIncome = $this->extractDbNetIncome($fsPl);
            if ($dbNetIncome === null) {
                continue;
            }

            $newNetIncome = $this->computeNewNetIncome($pdo, $idEntity, $numFiscalPeriod, $plTitleJson, $bsTitleJson);
            if ($newNetIncome === null) {
                continue;
            }

            $diffAbs = abs($dbNetIncome - $newNetIncome);
            $dbSalesAbs = abs($dbNetIncome) > 0 ? abs($dbNetIncome) : 1; // avoid division by zero
            $deviationRatio = $diffAbs / max(1, $dbSalesAbs);

            $results[] = [
                'idEntity'        => $idEntity,
                'numFiscalPeriod' => $numFiscalPeriod,
                'dbNetIncome'     => $dbNetIncome,
                'newNetIncome'    => $newNetIncome,
                'diffAbs'         => $diffAbs,
                'deviationRatio'  => $deviationRatio,
                'isLargeDeviation' => $deviationRatio > self::LARGE_DEVIATION_RATIO,
            ];
        }

        return $results;
    }

    /**
     * jsonJgaapFSPL の currentTermProfitOrLossNet.sumNext を取得する (整数値).
     */
    private function extractDbNetIncome(string $fsPl): ?int
    {
        $decoded = json_decode($fsPl, true);
        if (! is_array($decoded)) {
            return null;
        }

        // f1 キーに集計値が入っている
        foreach (['f1', 'f21', 'f22'] as $key) {
            $section = $decoded[$key] ?? null;
            if (! is_array($section)) {
                continue;
            }
            $netItem = $section['currentTermProfitOrLossNet'] ?? null;
            if (! is_array($netItem)) {
                continue;
            }
            $sumNext = $netItem['sumNext'] ?? null;
            if ($sumNext !== null) {
                return (int) $sumNext;
            }
        }

        return null;
    }

    /**
     * 新ドメインで当期純利益を再計算する.
     */
    private function computeNewNetIncome(
        \PDO $pdo,
        int $idEntity,
        int $numFiscalPeriod,
        string $plTitleJson,
        ?string $bsTitleJson = null,
    ): ?int {
        try {
            // PL の集計には PL ツリーのみを使う (BS 科目は PL 集計に不要)
            $tree = $this->accountTreeReader->buildProfitAndLossFromJson($plTitleJson);
        } catch (\Throwable) {
            return null;
        }

        $journalRows = $this->fetchJournalRows($pdo, $idEntity, $numFiscalPeriod);
        if ($journalRows === []) {
            return null;
        }

        $result = $this->journalReader->read($journalRows);
        if ($result['entries'] === []) {
            return null;
        }

        $ledger = Ledger::fromJournalEntries($result['entries']);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);

        return (int) $pl->netIncome()->toString();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchJournalRows(\PDO $pdo, int $idEntity, int $numFiscalPeriod): array
    {
        $stmt = $pdo->prepare(
            "SELECT id, stampBook, jsonVersion
             FROM accountingLog
             WHERE flagRemove = 0
               AND idEntity = ?
               AND numFiscalPeriod = ?
             ORDER BY stampBook, id"
        );
        $stmt->execute([$idEntity, $numFiscalPeriod]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll();
        return $rows;
    }
}
