<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\TrialBalanceDto;
use App\Domain\Ledger\Ledger;
use App\Domain\TrialBalance\OpeningBalances;
use App\Domain\TrialBalance\TrialBalance;
use App\Infrastructure\Persistence\AccountTreeRepository;
use App\Infrastructure\Persistence\JournalRepository;

/**
 * 試算表構築サービス.
 *
 * JournalRepository + AccountTreeRepository を組み合わせて
 * TrialBalance ドメインオブジェクトを構築し、UI 向け配列に変換する.
 */
final class TrialBalanceService
{
    public function __construct(
        private readonly JournalRepository $journalRepository,
        private readonly AccountTreeRepository $treeRepository,
    ) {
    }

    /**
     * 試算表を構築して配列形式で返す.
     *
     * @return array{rows: array<string, array<string, mixed>>}
     */
    public function build(
        int $idEntity,
        int $numFiscalPeriod,
        OpeningBalances $opening,
    ): array {
        $tree          = $this->treeRepository->loadCombinedTree($idEntity, $numFiscalPeriod);
        $journalRows   = $this->journalRepository->findByEntityAndPeriod($idEntity, $numFiscalPeriod);
        $ledger        = Ledger::fromJournalEntries($journalRows);
        $trialBalance  = TrialBalance::build($tree, $opening, $ledger);

        $dtoRows = [];
        foreach ($trialBalance->rows() as $id => $row) {
            $dto = new TrialBalanceDto(
                id: $id,
                title: $row->accountTitle()->title(),
                opening: (int) $row->opening()->toString(),
                periodDebits: (int) $row->periodDebits()->toString(),
                periodCredits: (int) $row->periodCredits()->toString(),
                closing: (int) $row->closing()->toString(),
            );
            $dtoRows[$id] = $dto->toArray();
        }

        return ['rows' => $dtoRows];
    }
}
