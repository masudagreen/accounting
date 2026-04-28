<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

/**
 * 株主資本等変動計算書における純資産の小区分.
 *
 * 元実装の JGAAP 純資産区分に対応:
 *   - CapitalStock       資本金
 *   - CapitalSurplus     資本剰余金
 *   - RetainedEarnings   利益剰余金
 *   - TreasuryStock      自己株式 (通常マイナス残高)
 *   - Other              その他の包括利益累計額等
 */
enum EquitySection: string
{
    case CapitalStock     = 'capitalStock';
    case CapitalSurplus   = 'capitalSurplus';
    case RetainedEarnings = 'retainedEarnings';
    case TreasuryStock    = 'treasuryStock';
    case Other            = 'other';
}
