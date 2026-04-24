<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceQueryInterface;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Support\Decimal\Decimal;

/**
 * In-memory {@see TrialBalanceQueryInterface} for application-layer tests.
 *
 * We drive it with a fixed list of posted journal lines and let the fake run
 * a plain PHP aggregation so the tests exercise the use case's orchestration
 * without needing a database.
 */
final class InMemoryTrialBalanceQuery implements TrialBalanceQueryInterface
{
    /** @var list<array{entityId:string, fiscalTermId:string, date:DateTimeImmutable, accountId:string, accountCode:string, accountName:string, category:string, normalSide:string, side:string, amount:string}> */
    private array $lines = [];

    private ?DateTimeImmutable $latestSnapshot = null;

    public function addLine(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $date,
        string $accountId,
        string $accountCode,
        string $accountName,
        string $category,
        string $normalSide,
        string $side,
        string $amount,
    ): void {
        $this->lines[] = [
            'entityId'     => $entityId,
            'fiscalTermId' => $fiscalTermId,
            'date'         => $date,
            'accountId'    => $accountId,
            'accountCode'  => $accountCode,
            'accountName'  => $accountName,
            'category'     => $category,
            'normalSide'   => $normalSide,
            'side'         => $side,
            'amount'       => $amount,
        ];
    }

    public function setLatestSnapshot(?DateTimeImmutable $date): void
    {
        $this->latestSnapshot = $date;
    }

    public function queryByPeriod(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): TrialBalance {
        /** @var array<string, array{code:string, name:string, category:string, normalSide:string, debit:string, credit:string, count:int}> $bucket */
        $bucket = [];
        foreach ($this->lines as $line) {
            if ($line['entityId'] !== $entityId || $line['fiscalTermId'] !== $fiscalTermId) {
                continue;
            }
            if ($line['date'] < $from || $line['date'] > $to) {
                continue;
            }
            $id = $line['accountId'];
            if (!isset($bucket[$id])) {
                $bucket[$id] = [
                    'code'       => $line['accountCode'],
                    'name'       => $line['accountName'],
                    'category'   => $line['category'],
                    'normalSide' => $line['normalSide'],
                    'debit'      => '0.0000',
                    'credit'     => '0.0000',
                    'count'      => 0,
                ];
            }
            if ($line['side'] === 'debit') {
                $bucket[$id]['debit'] = Decimal::add($bucket[$id]['debit'], $line['amount']);
            } else {
                $bucket[$id]['credit'] = Decimal::add($bucket[$id]['credit'], $line['amount']);
            }
            $bucket[$id]['count']++;
        }
        $rows = [];
        foreach ($bucket as $id => $b) {
            $rows[] = TrialBalanceRow::compute(
                accountTitleId: $id,
                accountTitleCode: $b['code'],
                accountTitleName: $b['name'],
                accountCategory: $b['category'],
                normalSide: $b['normalSide'],
                debitTotal: $b['debit'],
                creditTotal: $b['credit'],
                lineCount: $b['count'],
            );
        }
        usort(
            $rows,
            static fn (TrialBalanceRow $a, TrialBalanceRow $b): int => strcmp($a->accountTitleCode, $b->accountTitleCode),
        );

        return new TrialBalance(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $from,
            toDate: $to,
            currencyCode: 'JPY',
            rows: $rows,
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
    }

    public function latestSnapshotDate(string $entityId, string $fiscalTermId): ?DateTimeImmutable
    {
        return $this->latestSnapshot;
    }
}
