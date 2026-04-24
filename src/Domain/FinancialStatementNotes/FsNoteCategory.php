<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatementNotes;

/**
 * Category classifier for 注記表 entries (会社法 施行規則 101〜129 条).
 *
 * The values match the MariaDB `chk_fn__category` CHECK constraint declared
 * in `scripts/migrate/0018_fs_notes.sql`; any addition here MUST be mirrored
 * in the SQL constraint and in the PDF template's section ordering.
 */
enum FsNoteCategory: string
{
    case AccountingPolicy     = 'accounting_policy';
    case BalanceSheetNotes    = 'balance_sheet_notes';
    case PlNotes              = 'pl_notes';
    case SsNotes              = 'ss_notes';
    case RelatedParty         = 'related_party';
    case ContingentLiability  = 'contingent_liability';
    case Other                = 'other';

    /**
     * Human-readable Japanese label for PDF / UI rendering.
     */
    public function jaLabel(): string
    {
        return match ($this) {
            self::AccountingPolicy    => '重要な会計方針',
            self::BalanceSheetNotes   => '貸借対照表に関する注記',
            self::PlNotes             => '損益計算書に関する注記',
            self::SsNotes             => '株主資本等変動計算書に関する注記',
            self::RelatedParty        => '関連当事者との取引に関する注記',
            self::ContingentLiability => '偶発債務に関する注記',
            self::Other               => 'その他の注記',
        };
    }

    /**
     * Presentation order on the 注記表 PDF: policies come first, then BS/PL/SS
     * notes, then related-party / contingent, then catch-all "other".
     */
    public function displayOrder(): int
    {
        return match ($this) {
            self::AccountingPolicy    => 10,
            self::BalanceSheetNotes   => 20,
            self::PlNotes             => 30,
            self::SsNotes             => 40,
            self::RelatedParty        => 50,
            self::ContingentLiability => 60,
            self::Other               => 90,
        };
    }
}
