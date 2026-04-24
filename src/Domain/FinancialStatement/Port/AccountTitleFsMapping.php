<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port;

/**
 * Mapping from a single account title to a FS section.
 *
 * One account title can appear on both the BS and the PL (e.g. "繰越利益剰余金"
 * carries retained earnings on the BS and net income flows into it on the PL)
 * — that's why the unique key on `account_title_fs_mappings` is
 * (entity_id, account_title_id, fs_kind).
 *
 * `sign` encodes whether the amount should be added (+1) or subtracted (-1)
 * inside its section. Legacy `CalcAccountTitleFS::_getValueFS()` derived this
 * from the `flagDebit` of both the account title and the FS line; we precompute
 * and store it so the runtime calculation stays a simple Σ.
 */
final readonly class AccountTitleFsMapping
{
    public function __construct(
        public string $accountTitleId,
        public FsKind $kind,
        public string $sectionCode,
        public int $sign,
        public int $sortOrder,
        public ?string $displayLabel,
    ) {
    }
}
