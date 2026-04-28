<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Journal\JournalLine;
use App\Infrastructure\Persistence\JournalRepository;

/**
 * 仕訳の参照・集計サービス.
 *
 * ドメイン値オブジェクトを UI 向け配列に変換して返す.
 */
final class JournalService
{
    public function __construct(
        private readonly JournalRepository $repository,
    ) {
    }

    /**
     * 指定事業体・会計期の仕訳一覧を配列形式で返す.
     *
     * @return list<array{
     *   date: string,
     *   totalDebits: int,
     *   totalCredits: int,
     *   debits: list<array{accountTitleId: string, amount: int}>,
     *   credits: list<array{accountTitleId: string, amount: int}>,
     * }>
     */
    public function getEntries(int $idEntity, int $numFiscalPeriod): array
    {
        $rows = $this->repository->findByEntityAndPeriod($idEntity, $numFiscalPeriod);

        $result = [];
        foreach ($rows as $row) {
            $date  = $row['date'];
            $entry = $row['entry'];

            $result[] = [
                'date'         => $date->format('Y-m-d'),
                'totalDebits'  => (int) $entry->totalDebits()->toString(),
                'totalCredits' => (int) $entry->totalCredits()->toString(),
                'debits'       => $this->linesToArray($entry->debits()),
                'credits'      => $this->linesToArray($entry->credits()),
            ];
        }

        return $result;
    }

    /**
     * @param list<JournalLine> $lines
     * @return list<array{accountTitleId: string, amount: int}>
     */
    private function linesToArray(array $lines): array
    {
        return array_map(
            static fn (JournalLine $line): array => [
                'accountTitleId' => $line->accountTitleId(),
                'amount'         => (int) $line->amount()->toString(),
            ],
            $lines,
        );
    }
}
