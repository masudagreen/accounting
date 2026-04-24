<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FixedAsset\DepreciationScheduleRepositoryInterface;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Decimal\Decimal;

/**
 * Post depreciation journals for every unposted schedule entry in a fiscal
 * term.
 *
 * Creates one journal per asset:
 *   Dr  減価償却費                 (depreciation_expense_account_title_id)
 *       Cr 減価償却累計額          (accumulated_depreciation_account_title_id)
 *
 * The use case is idempotent: entries already flagged `is_posted=1` are
 * skipped. Assets with zero depreciation for the period are also skipped to
 * keep the journal set minimal.
 */
final readonly class PostDepreciationJournalUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
        private DepreciationScheduleRepositoryInterface $schedules,
        private JournalRepositoryInterface $journals,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(PostDepreciationJournalInput $input): PostDepreciationJournalOutput
    {
        $entries = $this->schedules->findByEntityAndFiscalTerm(
            $input->entityId,
            $input->fiscalTermId,
        );
        $postings = [];
        foreach ($entries as $entry) {
            if ($entry->isPosted) {
                $postings[] = [
                    'fixedAssetId'       => $entry->fixedAssetId,
                    'scheduleEntryId'    => $entry->id,
                    'journalEntryId'     => (string) $entry->postedJournalEntryId,
                    'depreciationAmount' => $entry->depreciationAmount,
                    'skipped'            => true,
                ];
                continue;
            }
            if (Decimal::compare($entry->depreciationAmount, '0.0000') <= 0) {
                continue;
            }

            $asset = $this->assets->findById($entry->fixedAssetId);
            if ($asset === null) {
                continue;
            }
            if ($asset->depreciationExpenseAccountTitleId === null
                || $asset->accumulatedDepreciationAccountTitleId === null) {
                throw ValidationException::withErrors([
                    'asset.' . $asset->assetCode => ['depreciation expense / accumulated depreciation accounts must be set to post.'],
                ]);
            }

            $now = $this->clock->getCurrentTime();
            $amount = Decimal::normalize($entry->depreciationAmount);
            $journalId = $this->ulids->generate();
            $lines = [
                new JournalLine(
                    id: $this->ulids->generate(),
                    lineNo: 1,
                    side: JournalLine::SIDE_DEBIT,
                    accountTitleId: $asset->depreciationExpenseAccountTitleId,
                    subAccountTitleId: null,
                    amount: $amount,
                    taxRatePercent: '0.00',
                    taxAmount: '0.0000',
                    isTaxReduced: false,
                    memo: '減価償却費 ' . $asset->assetCode,
                    bookedAt: $now,
                ),
                new JournalLine(
                    id: $this->ulids->generate(),
                    lineNo: 2,
                    side: JournalLine::SIDE_CREDIT,
                    accountTitleId: $asset->accumulatedDepreciationAccountTitleId,
                    subAccountTitleId: null,
                    amount: $amount,
                    taxRatePercent: '0.00',
                    taxAmount: '0.0000',
                    isTaxReduced: false,
                    memo: '減価償却累計額 ' . $asset->assetCode,
                    bookedAt: $now,
                ),
            ];

            $journal = new Journal(
                id: $journalId,
                entityId: $asset->entityId,
                fiscalTermId: $input->fiscalTermId,
                journalDate: $entry->periodEndDate,
                bookedAt: $now,
                summary: sprintf('減価償却 [%s] %s', $asset->assetCode, $asset->assetName),
                totalAmount: $amount,
                currencyCode: 'JPY',
                status: 'posted',
                source: 'manual',
                sourceReceiptId: null,
                createdBy: $input->postedBy,
                approvedBy: $input->postedBy,
                approvedAt: $now,
                createdAt: $now,
                updatedAt: $now,
                deletedAt: null,
                lines: $lines,
            );
            $this->journals->save($journal);

            $postedEntry = $entry->markPosted($journalId, $now);
            $this->schedules->save($postedEntry);

            $postings[] = [
                'fixedAssetId'       => $asset->id,
                'scheduleEntryId'    => $entry->id,
                'journalEntryId'     => $journalId,
                'depreciationAmount' => $amount,
                'skipped'            => false,
            ];
        }
        return new PostDepreciationJournalOutput($postings);
    }
}
