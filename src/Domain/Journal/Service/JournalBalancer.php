<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal\Service;

use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Support\Decimal\Decimal;

/**
 * Stateless domain service that validates the debit/credit balance invariant
 * across a set of {@see JournalLine} instances.
 *
 * Mirrors {@see \Rucaro\Domain\Journal\Journal::balance()} so new flows (e.g.
 * draft import, bulk upload) can reuse the check without going through the
 * aggregate constructor. Kept here explicitly for the Phase 4.2 expansion
 * where reversal / reapplication reshape lines outside of the aggregate.
 */
final class JournalBalancer
{
    /**
     * @param list<JournalLine> $lines
     * @return string DECIMAL(18,4) total (debit == credit) as a canonical string
     */
    public function balance(array $lines): string
    {
        if (count($lines) < 2) {
            throw InvariantViolationException::for('journal.min_lines', [
                'expected' => 2,
                'actual'   => count($lines),
            ]);
        }

        $debit  = '0.0000';
        $credit = '0.0000';
        foreach ($lines as $line) {
            if ($line->isDebit()) {
                $debit = Decimal::add($debit, $line->amount);
            } else {
                $credit = Decimal::add($credit, $line->amount);
            }
        }

        if (Decimal::compare($debit, '0.0000') === 0) {
            throw InvariantViolationException::for('journal.must_have_debit', [
                'debit_total'  => $debit,
                'credit_total' => $credit,
            ]);
        }
        if (Decimal::compare($credit, '0.0000') === 0) {
            throw InvariantViolationException::for('journal.must_have_credit', [
                'debit_total'  => $debit,
                'credit_total' => $credit,
            ]);
        }
        if (Decimal::compare($debit, $credit) !== 0) {
            throw InvariantViolationException::for('journal.must_balance', [
                'debit_total'  => $debit,
                'credit_total' => $credit,
            ]);
        }

        return Decimal::normalize($debit);
    }
}
