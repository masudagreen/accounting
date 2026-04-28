<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\BalanceSheetDto;
use App\Application\Dto\ProfitAndLossDto;
use App\Domain\FinancialStatement\BalanceSheetBuilder;
use App\Domain\FinancialStatement\ProfitAndLossBuilder;
use App\Domain\Ledger\Ledger;
use App\Domain\TrialBalance\OpeningBalances;
use App\Domain\TrialBalance\TrialBalance;
use App\Infrastructure\Persistence\AccountTreeRepository;
use App\Infrastructure\Persistence\JournalRepository;

/**
 * 財務諸表 (PL / BS) 構築サービス.
 */
final class FinancialStatementService
{
    public function __construct(
        private readonly JournalRepository $journalRepository,
        private readonly AccountTreeRepository $treeRepository,
    ) {
    }

    /**
     * 損益計算書 DTO を配列形式で返す.
     *
     * @return array<string, int>
     */
    public function buildProfitAndLoss(
        int $idEntity,
        int $numFiscalPeriod,
        OpeningBalances $opening,
    ): array {
        [$tree, $tb] = $this->buildTrialBalance($idEntity, $numFiscalPeriod, $opening);

        $pl = ProfitAndLossBuilder::build($tree, $tb);

        $dto = new ProfitAndLossDto(
            sales: (int) $pl->sales()->toString(),
            costOfSales: (int) $pl->costOfSales()->toString(),
            grossProfit: (int) $pl->grossProfit()->toString(),
            sellingAndAdmin: (int) $pl->sellingAndAdmin()->toString(),
            operatingIncome: (int) $pl->operatingIncome()->toString(),
            nonOperatingIncome: (int) $pl->nonOperatingIncome()->toString(),
            nonOperatingExpenses: (int) $pl->nonOperatingExpenses()->toString(),
            ordinaryIncome: (int) $pl->ordinaryIncome()->toString(),
            extraordinaryIncome: (int) $pl->extraordinaryIncome()->toString(),
            extraordinaryLosses: (int) $pl->extraordinaryLosses()->toString(),
            incomeBeforeTax: (int) $pl->incomeBeforeTax()->toString(),
            tax: (int) $pl->tax()->toString(),
            netIncome: (int) $pl->netIncome()->toString(),
        );

        return $dto->toArray();
    }

    /**
     * 貸借対照表 DTO を配列形式で返す.
     *
     * @return array<string, int>
     */
    public function buildBalanceSheet(
        int $idEntity,
        int $numFiscalPeriod,
        OpeningBalances $opening,
    ): array {
        [$tree, $tb] = $this->buildTrialBalance($idEntity, $numFiscalPeriod, $opening);

        $pl = ProfitAndLossBuilder::build($tree, $tb);
        $bs = BalanceSheetBuilder::build($tree, $tb, $pl);

        $dto = new BalanceSheetDto(
            totalAssets: (int) $bs->totalAssets()->toString(),
            totalLiabilities: (int) $bs->totalLiabilities()->toString(),
            totalEquity: (int) $bs->totalEquity()->toString(),
        );

        return $dto->toArray();
    }

    /**
     * @return array{0: \App\Domain\AccountTitle\AccountTree, 1: TrialBalance}
     */
    private function buildTrialBalance(
        int $idEntity,
        int $numFiscalPeriod,
        OpeningBalances $opening,
    ): array {
        $tree        = $this->treeRepository->loadCombinedTree($idEntity, $numFiscalPeriod);
        $journalRows = $this->journalRepository->findByEntityAndPeriod($idEntity, $numFiscalPeriod);
        $ledger      = Ledger::fromJournalEntries($journalRows);
        $tb          = TrialBalance::build($tree, $opening, $ledger);

        return [$tree, $tb];
    }
}
