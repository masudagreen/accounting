<?php

declare(strict_types=1);

namespace App\Domain\Banks;

/**
 * 銀行 Web 取込パーサのインターフェース.
 *
 * 各銀行固有のパーサはこのインターフェースを実装し、
 * BankAdapterRegistry に銀行コードと共に登録する.
 *
 * 将来の実装予定:
 *  - JapannetBankAdapter  (ジャパンネット銀行 CSV)
 *  - JapanpostBankAdapter (ゆうちょ銀行 CSV)
 *  - JibunBankAdapter     (じぶん銀行 CSV)
 *  - SumisinNetBankAdapter (住信SBIネット銀行 CSV)
 *  - SurugaBankAdapter    (スルガ銀行 CSV)
 */
interface BankStatementImporter
{
    /**
     * 銀行 Web からダウンロードした生データを解析し、明細リストを返す.
     *
     * @param string $rawData 銀行から取得した生データ (CSV, HTML 等)
     * @return list<BankStatement>
     */
    public function importStatements(string $rawData): array;
}
