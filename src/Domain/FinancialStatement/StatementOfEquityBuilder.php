<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * 株主資本等変動計算書を構築する.
 *
 * 入力:
 *   - openingBalances: 各 EquitySection の期首残高 (section->value => Money)
 *   - changes:         当期の変動一覧 (EquityChange[])
 *
 * 出力:
 *   StatementOfEquity (期首・変動・期末を保持する値オブジェクト)
 */
final class StatementOfEquityBuilder
{
    /**
     * @param array<string, Money> $openingBalances  EquitySection->value => 期首残高
     * @param list<EquityChange>   $changes
     */
    public static function build(
        array $openingBalances,
        array $changes,
    ): StatementOfEquity {
        // 期末残高を導出: 期首 + セクション別変動合計
        $closingBalances = $openingBalances;

        foreach ($changes as $change) {
            $key = $change->section()->value;
            $current = $closingBalances[$key] ?? Money::zero();
            $closingBalances[$key] = $current->plus($change->amount());
        }

        // 全セクションを網羅するため openingBalances にも closing にもないキーをマージ
        $allKeys = array_unique(
            array_merge(array_keys($openingBalances), array_keys($closingBalances)),
        );

        // 純資産合計: 期首
        $totalOpening = Money::zero();
        foreach ($allKeys as $key) {
            $totalOpening = $totalOpening->plus($openingBalances[$key] ?? Money::zero());
        }

        // 変動合計
        $totalChange = Money::zero();
        foreach ($changes as $change) {
            $totalChange = $totalChange->plus($change->amount());
        }

        // 純資産合計: 期末
        $totalClosing = $totalOpening->plus($totalChange);

        return new StatementOfEquity(
            openingBalances: $openingBalances,
            changes: array_values($changes),
            closingBalances: $closingBalances,
            totalEquityOpening: $totalOpening,
            totalChange: $totalChange,
            totalEquityClosing: $totalClosing,
        );
    }
}
