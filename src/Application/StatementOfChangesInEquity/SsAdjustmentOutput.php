<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;

/**
 * Standard write-path envelope — the HTTP controller reads
 * `->adjustment` and passes it to the JSON serializer. Lives in its
 * own file so adding new UseCase verbs does not force an import
 * bloom on the consumer side.
 */
final readonly class SsAdjustmentOutput
{
    public function __construct(public SsManualAdjustment $adjustment)
    {
    }
}
