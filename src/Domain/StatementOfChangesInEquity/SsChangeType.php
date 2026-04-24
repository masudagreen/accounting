<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

/**
 * Change-type enumeration for rows on the 株主資本等変動計算書.
 *
 * - `Dividend`           : 剰余金の配当 (retained earnings ↓)
 * - `NewIssue`           : 新株の発行 (capital stock ↑, capital surplus ↑)
 * - `TreasuryPurchase`   : 自己株式の取得 (treasury stock ↓)
 * - `TreasuryDispose`    : 自己株式の処分 (treasury stock ↑, capital surplus ±)
 * - `NetIncome`          : 当期純利益 (retained earnings ↑ — auto from PL)
 * - `Other`              : その他変動 (valuation adj, SAR grants, etc.)
 *
 * Kept deliberately coarse: the intent of Wave 6-H-2 is the reviewer
 * UX, not a taxonomy deep enough to satisfy JGAAP footnotes. Finer-
 * grained categorisation (e.g. reserves / appropriations) is expected
 * to live in ADR-018 alongside financial-statement notes.
 */
enum SsChangeType: string
{
    case Dividend         = 'dividend';
    case NewIssue         = 'new_issue';
    case TreasuryPurchase = 'treasury_purchase';
    case TreasuryDispose  = 'treasury_dispose';
    case NetIncome        = 'net_income';
    case Other            = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Dividend         => '剰余金の配当',
            self::NewIssue         => '新株の発行',
            self::TreasuryPurchase => '自己株式の取得',
            self::TreasuryDispose  => '自己株式の処分',
            self::NetIncome        => '当期純利益',
            self::Other            => 'その他変動',
        };
    }

    /**
     * Whether the change typically originates from the legacy
     * `manual adjustment` table (as opposed to an auto-detected
     * journal).
     */
    public function isManual(): bool
    {
        return $this !== self::NetIncome;
    }
}
