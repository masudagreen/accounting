<?php

declare(strict_types=1);

namespace Rucaro\Application\Ledger;

use Rucaro\Domain\Ledger\Ledger;

/**
 * Output envelope for {@see QueryLedgerUseCase}.
 *
 * Intentionally thin: the Ledger aggregate already carries every field
 * the HTTP layer needs. The envelope exists so the shape stays consistent
 * with every other UseCase in the core (Journal / TrialBalance / FS).
 */
final readonly class QueryLedgerUseCaseOutput
{
    public function __construct(
        public Ledger $ledger,
    ) {
    }
}
