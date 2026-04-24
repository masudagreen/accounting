<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port;

/**
 * Enum identifying whether a section / mapping belongs to the Balance Sheet
 * or the Profit & Loss statement.
 *
 * Cash flow sections are not represented here: Phase 6-A ports the BS/PL
 * mapping tables used by the legacy Jpn_FinancialStatement module. Cash
 * flow remains stubbed in the {@see \Rucaro\Application\FinancialStatement\Simplified}
 * port until Wave 6-C.
 */
enum FsKind: string
{
    case BalanceSheet = 'bs';
    case ProfitAndLoss = 'pl';

    public static function fromString(string $raw): self
    {
        $normalised = strtolower($raw);
        return match ($normalised) {
            'bs', 'balance_sheet', 'balancesheet' => self::BalanceSheet,
            'pl', 'profit_and_loss', 'profitandloss' => self::ProfitAndLoss,
            default => throw new \InvalidArgumentException('Unknown FsKind: ' . $raw),
        };
    }
}
