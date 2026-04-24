<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

final readonly class PostDepreciationJournalOutput
{
    /**
     * @param list<array{
     *     fixedAssetId: string,
     *     scheduleEntryId: string,
     *     journalEntryId: string,
     *     depreciationAmount: string,
     *     skipped: bool,
     * }> $postings
     */
    public function __construct(public array $postings)
    {
    }
}
