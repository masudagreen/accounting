<?php

declare(strict_types=1);

namespace App\Domain\ConsumptionTax;

/**
 * 消費税の経理処理方式 (元実装 `accountingEntityJpn.flagConsumptionTaxWithoutCalc`).
 *
 *  - Exclusive (外税): 税抜入力 + 税額別計算
 *  - Inclusive (内税): 税込入力 + 税抜額逆算
 *  - Separate (別記): ユーザが税額を別個に入力する (システム側では計算しない)
 *
 * 元実装の値:
 *  flagConsumptionTaxWithoutCalc = 1=in (内税) / 2=out (外税) / 3=another (別記)
 */
enum TaxTreatment: string
{
    case Inclusive = 'inclusive';   // 1: 内税
    case Exclusive = 'exclusive';   // 2: 外税
    case Separate  = 'separate';    // 3: 別記
}
