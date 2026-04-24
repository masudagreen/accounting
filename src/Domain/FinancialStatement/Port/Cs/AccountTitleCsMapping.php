<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Cs;

/**
 * Mapping from a single account title to a CS section.
 *
 * One account title is mapped to at most one CS section per entity — CS
 * entries flow from movement (change over the period) rather than ending
 * balances, so we don't need the BS/PL dual-mapping trick.
 *
 * `sign` encodes addition (+1) or subtraction (-1) inside the section.
 * Legacy `CalcAccountTitleFSCS::_getValueFS()` derived the sign from the
 * combination of `flagMethod` (net/sumDebit/sumCredit) and the Plus/Minus
 * leg; we collapse that to a single `+1/-1` at mapping time so the runtime
 * calculation stays a simple Σ of signed movements.
 *
 * `flowCategory` identifies which of the three CS buckets the mapping feeds
 * (operating / investing / financing).
 *
 * `isWorkingCapital` = `true` for operating-side accounts whose increase is
 * treated as a cash decrease (売掛金 ↑ means cash ↓). The builder flips the
 * sign on those during the operating section roll-up so the rendered line is
 * the cash-flow impact rather than the raw balance change.
 */
final readonly class AccountTitleCsMapping
{
    public function __construct(
        public string $accountTitleId,
        public string $sectionCode,
        public CsFlowCategory $flowCategory,
        public int $sign,
        public bool $isWorkingCapital,
        public int $sortOrder,
        public ?string $displayLabel,
    ) {
    }
}
