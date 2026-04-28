<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

/**
 * 株主資本等変動計算書における変動事由の種別.
 */
enum EquityChangeType: string
{
    /** 新株発行 */
    case NewSharesIssued = 'newSharesIssued';

    /** 剰余金の配当 */
    case DividendsDeclared = 'dividendsDeclared';

    /** 当期純利益 */
    case NetIncome = 'netIncome';

    /** 自己株式の取得 */
    case TreasuryStockAcquisition = 'treasuryStockAcquisition';

    /** 自己株式の処分 */
    case TreasuryStockDisposal = 'treasuryStockDisposal';

    /** その他 */
    case Other = 'other';
}
