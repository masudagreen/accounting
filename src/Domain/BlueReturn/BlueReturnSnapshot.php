<?php

declare(strict_types=1);

namespace Rucaro\Domain\BlueReturn;

/**
 * Immutable snapshot of the 4-page 青色申告決算書 payload.
 *
 * Modeled as a plain value object holding four nested arrays — one per
 * page — because the tax-authority form layout changes every few years
 * and we need structural latitude to absorb new fields without a DB
 * migration. See ADR-016 §2 for the snapshot-vs-columns rationale.
 *
 * Pages:
 *   1. 損益計算書 (P&L)              — page1_pl
 *   2. 月別売上・仕入・給料賃金     — page2_monthly
 *   3. 減価償却 / 貸倒 / 地代 / 利子 / 税理士 — page3_breakdown
 *   4. 貸借対照表 (BS, individual)  — page4_bs
 */
final readonly class BlueReturnSnapshot
{
    /**
     * @param array<string, mixed>  $page1Pl
     * @param array<string, mixed>  $page2Monthly
     * @param array<string, mixed>  $page3Breakdown
     * @param array<string, mixed>  $page4Bs
     */
    public function __construct(
        public array $page1Pl,
        public array $page2Monthly,
        public array $page3Breakdown,
        public array $page4Bs,
    ) {
    }

    /**
     * Serialise to the wire / column representation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return [
            'page1_pl'        => $this->page1Pl,
            'page2_monthly'   => $this->page2Monthly,
            'page3_breakdown' => $this->page3Breakdown,
            'page4_bs'        => $this->page4Bs,
        ];
    }

    /**
     * Build a snapshot from the column JSON.
     *
     * Missing keys fall back to empty arrays so older rows (written
     * before a new page field was added) still hydrate cleanly.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        $page1 = $raw['page1_pl'] ?? [];
        $page2 = $raw['page2_monthly'] ?? [];
        $page3 = $raw['page3_breakdown'] ?? [];
        $page4 = $raw['page4_bs'] ?? [];
        return new self(
            page1Pl: is_array($page1) ? $page1 : [],
            page2Monthly: is_array($page2) ? $page2 : [],
            page3Breakdown: is_array($page3) ? $page3 : [],
            page4Bs: is_array($page4) ? $page4 : [],
        );
    }

    /**
     * Produce an all-zero snapshot shaped for the given form type.
     *
     * Used by {@see Service\BlueReturnBuilder} as a starting skeleton
     * when no actuals are available yet — e.g. a freshly-created form.
     */
    public static function empty(BlueReturnFormType $formType): self
    {
        return new self(
            page1Pl: [
                'formType'    => $formType->value,
                'revenue'     => [],
                'costOfSales' => [],
                'expenses'    => [],
                'netIncome'   => '0',
            ],
            page2Monthly: [
                'months'  => [],
                'totals'  => [
                    'sales'    => '0',
                    'purchase' => '0',
                    'salary'   => '0',
                ],
            ],
            page3Breakdown: [
                'depreciation'   => [],
                'allowance'      => [],
                'rent'           => [],
                'interest'       => [],
                'taxAccountant'  => [],
            ],
            page4Bs: [
                'assets'      => [],
                'liabilities' => [],
                'equity'      => [],
            ],
        );
    }
}
