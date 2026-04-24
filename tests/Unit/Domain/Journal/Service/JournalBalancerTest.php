<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal\Service;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\Service\JournalBalancer;
use Rucaro\Support\Decimal\Decimal;

#[CoversClass(JournalBalancer::class)]
final class JournalBalancerTest extends TestCase
{
    /**
     * Drives the balancer with 30+ randomly sized but *balanced* journal
     * line sets. Serves as a lightweight property-based check — each case
     * verifies the service computes the same total both sides of the ledger.
     *
     * @param list<array{0: string, 1: string}> $debits  [accountId, amount]
     * @param list<array{0: string, 1: string}> $credits [accountId, amount]
     */
    #[DataProvider('balancedCases')]
    public function testBalancedLinesReturnDebitSum(array $debits, array $credits, string $expectedTotal): void
    {
        $lines = $this->buildLines($debits, $credits);
        $balancer = new JournalBalancer();
        self::assertSame($expectedTotal, $balancer->balance($lines));
    }

    /**
     * @return list<array{0: list<array{0: string, 1: string}>, 1: list<array{0: string, 1: string}>, 2: string}>
     */
    public static function balancedCases(): array
    {
        $cases = [];
        $seed = 20260421;
        mt_srand($seed);

        for ($i = 0; $i < 32; $i++) {
            $numDebits  = mt_rand(1, 4);
            $numCredits = mt_rand(1, 4);
            // Pick a total first, then split into random shares on each side
            $total = mt_rand(100, 99999);

            $debits  = self::splitAmount($total, $numDebits, '01HW7K9B2QV7C8Y4ZACCTTL00D');
            $credits = self::splitAmount($total, $numCredits, '01HW7K9B2QV7C8Y4ZACCTTL00C');

            $cases[] = [$debits, $credits, Decimal::normalize(sprintf('%d.0000', $total))];
        }
        mt_srand();
        return $cases;
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    private static function splitAmount(int $total, int $parts, string $accountId): array
    {
        /** @var list<array{0: string, 1: string}> $out */
        $out = [];
        $remaining = $total;
        for ($k = 0; $k < $parts; $k++) {
            if ($k === $parts - 1) {
                $share = $remaining;
            } else {
                $share = mt_rand(1, max(1, (int) floor($remaining / ($parts - $k))));
            }
            $remaining -= $share;
            $out[] = [$accountId, sprintf('%d.0000', $share)];
        }
        return $out;
    }

    /**
     * Drives the balancer with 32 deliberately unbalanced cases to make sure
     * mismatches always raise {@see InvariantViolationException}.
     *
     * @param list<array{0: string, 1: string}> $debits
     * @param list<array{0: string, 1: string}> $credits
     */
    #[DataProvider('unbalancedCases')]
    public function testUnbalancedLinesRaise(array $debits, array $credits): void
    {
        $lines = $this->buildLines($debits, $credits);
        $balancer = new JournalBalancer();
        $this->expectException(InvariantViolationException::class);
        $balancer->balance($lines);
    }

    /**
     * @return list<array{0: list<array{0: string, 1: string}>, 1: list<array{0: string, 1: string}>}>
     */
    public static function unbalancedCases(): array
    {
        $cases = [];
        $seed = 20260422;
        mt_srand($seed);
        for ($i = 0; $i < 32; $i++) {
            $debit  = mt_rand(100, 9999);
            $delta  = mt_rand(1, 500);
            $credit = $debit + $delta; // guaranteed non-equal
            $cases[] = [
                [['01HW7K9B2QV7C8Y4ZACCTTL00D', sprintf('%d.0000', $debit)]],
                [['01HW7K9B2QV7C8Y4ZACCTTL00C', sprintf('%d.0000', $credit)]],
            ];
        }
        mt_srand();
        return $cases;
    }

    public function testFewerThanTwoLinesRaise(): void
    {
        $balancer = new JournalBalancer();
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/min_lines/');
        $balancer->balance([$this->line(1, 'debit', '100.0000')]);
    }

    public function testAllDebitsRaiseMustHaveCredit(): void
    {
        $balancer = new JournalBalancer();
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/must_have_credit/');
        $balancer->balance([
            $this->line(1, 'debit', '50.0000'),
            $this->line(2, 'debit', '50.0000'),
        ]);
    }

    public function testAllCreditsRaiseMustHaveDebit(): void
    {
        $balancer = new JournalBalancer();
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/must_have_debit/');
        $balancer->balance([
            $this->line(1, 'credit', '50.0000'),
            $this->line(2, 'credit', '50.0000'),
        ]);
    }

    /**
     * @param list<array{0: string, 1: string}> $debits
     * @param list<array{0: string, 1: string}> $credits
     * @return list<JournalLine>
     */
    private function buildLines(array $debits, array $credits): array
    {
        $lineNo = 0;
        /** @var list<JournalLine> $out */
        $out = [];
        foreach ($debits as [$_, $amount]) {
            $lineNo++;
            $out[] = $this->line($lineNo, 'debit', $amount);
        }
        foreach ($credits as [$_, $amount]) {
            $lineNo++;
            $out[] = $this->line($lineNo, 'credit', $amount);
        }
        return $out;
    }

    private function line(int $no, string $side, string $amount): JournalLine
    {
        return new JournalLine(
            id: sprintf('01HW7K9B2QV7C8Y4ZLINE%05d', $no),
            lineNo: $no,
            side: $side,
            accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
            subAccountTitleId: null,
            amount: $amount,
            taxRatePercent: '0.00',
            taxAmount: '0.0000',
            isTaxReduced: false,
            memo: '',
            bookedAt: new DateTimeImmutable('2026-04-21T00:00:00Z'),
        );
    }
}
