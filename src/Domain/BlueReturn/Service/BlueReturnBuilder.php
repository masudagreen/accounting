<?php

declare(strict_types=1);

namespace Rucaro\Domain\BlueReturn\Service;

use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Support\Decimal\Decimal;

/**
 * Pure-domain service that derives a {@see BlueReturnSnapshot} from
 * raw trial-balance rows and depreciation / fixed-asset outputs.
 *
 * Kept intentionally side-effect-free: no repositories, no clock. The
 * caller (UseCase) is responsible for fetching the source data and
 * persisting the resulting form. This lets the builder be exercised
 * with pure array fixtures in unit tests.
 *
 * NOTE: The builder produces a structural skeleton; line-level
 * classification ("which accounts belong to 売上原価 vs 経費") is a
 * separate mapping concern handled higher up. Here we just aggregate
 * the inputs the caller already classified.
 */
final class BlueReturnBuilder
{
    /**
     * @param array<string, string> $revenueByAccount     key = account label, value = decimal amount
     * @param array<string, string> $costOfSalesByAccount
     * @param array<string, string> $expensesByAccount
     * @param list<array{month:int,sales:string,purchase:string,salary:string}> $monthlyRows
     * @param array<string, list<array<string, mixed>>>                          $breakdown
     *     keys: depreciation / allowance / rent / interest / taxAccountant
     * @param array<string, string> $assetsByAccount
     * @param array<string, string> $liabilitiesByAccount
     * @param array<string, string> $equityByAccount
     */
    public function build(
        BlueReturnFormType $formType,
        array $revenueByAccount,
        array $costOfSalesByAccount,
        array $expensesByAccount,
        array $monthlyRows,
        array $breakdown,
        array $assetsByAccount,
        array $liabilitiesByAccount,
        array $equityByAccount,
    ): BlueReturnSnapshot {
        $revenueTotal   = self::sum($revenueByAccount);
        $cogsTotal      = self::sum($costOfSalesByAccount);
        $expensesTotal  = self::sum($expensesByAccount);

        // 所得 = 収入 − 売上原価 − 経費
        $netIncome = Decimal::add(
            $revenueTotal,
            self::negate(Decimal::add($cogsTotal, $expensesTotal)),
        );

        $monthlyTotals = [
            'sales'    => '0.0000',
            'purchase' => '0.0000',
            'salary'   => '0.0000',
        ];
        foreach ($monthlyRows as $row) {
            $monthlyTotals['sales']    = Decimal::add($monthlyTotals['sales'], $row['sales']);
            $monthlyTotals['purchase'] = Decimal::add($monthlyTotals['purchase'], $row['purchase']);
            $monthlyTotals['salary']   = Decimal::add($monthlyTotals['salary'], $row['salary']);
        }

        $page1 = [
            'formType'        => $formType->value,
            'revenue'         => self::toRows($revenueByAccount),
            'revenueTotal'    => Decimal::normalize($revenueTotal),
            'costOfSales'     => self::toRows($costOfSalesByAccount),
            'costOfSalesTotal'=> Decimal::normalize($cogsTotal),
            'expenses'        => self::toRows($expensesByAccount),
            'expensesTotal'   => Decimal::normalize($expensesTotal),
            'netIncome'       => Decimal::normalize($netIncome),
        ];

        $page2 = [
            'months' => array_map(
                static fn (array $r): array => [
                    'month'    => $r['month'],
                    'sales'    => Decimal::normalize($r['sales']),
                    'purchase' => Decimal::normalize($r['purchase']),
                    'salary'   => Decimal::normalize($r['salary']),
                ],
                $monthlyRows,
            ),
            'totals' => [
                'sales'    => Decimal::normalize($monthlyTotals['sales']),
                'purchase' => Decimal::normalize($monthlyTotals['purchase']),
                'salary'   => Decimal::normalize($monthlyTotals['salary']),
            ],
        ];

        $page3 = [
            'depreciation'  => $breakdown['depreciation']  ?? [],
            'allowance'     => $breakdown['allowance']     ?? [],
            'rent'          => $breakdown['rent']          ?? [],
            'interest'      => $breakdown['interest']      ?? [],
            'taxAccountant' => $breakdown['taxAccountant'] ?? [],
        ];

        $assetsTotal      = self::sum($assetsByAccount);
        $liabilitiesTotal = self::sum($liabilitiesByAccount);
        $equityTotal      = self::sum($equityByAccount);

        $page4 = [
            'assets'           => self::toRows($assetsByAccount),
            'assetsTotal'      => Decimal::normalize($assetsTotal),
            'liabilities'      => self::toRows($liabilitiesByAccount),
            'liabilitiesTotal' => Decimal::normalize($liabilitiesTotal),
            'equity'           => self::toRows($equityByAccount),
            'equityTotal'      => Decimal::normalize($equityTotal),
        ];

        return new BlueReturnSnapshot(
            page1Pl: $page1,
            page2Monthly: $page2,
            page3Breakdown: $page3,
            page4Bs: $page4,
        );
    }

    private static function negate(string $v): string
    {
        $n = Decimal::normalize($v);
        if ($n === '0.0000') {
            return $n;
        }
        return str_starts_with($n, '-') ? substr($n, 1) : '-' . $n;
    }

    /**
     * @param array<string, string> $byLabel
     */
    private static function sum(array $byLabel): string
    {
        $acc = '0.0000';
        foreach ($byLabel as $v) {
            $acc = Decimal::add($acc, $v);
        }
        return $acc;
    }

    /**
     * @param array<string, string> $byLabel
     * @return list<array{label:string, amount:string}>
     */
    private static function toRows(array $byLabel): array
    {
        $out = [];
        foreach ($byLabel as $label => $amount) {
            $out[] = [
                'label'  => (string) $label,
                'amount' => Decimal::normalize($amount),
            ];
        }
        return $out;
    }
}
