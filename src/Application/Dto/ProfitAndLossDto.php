<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * 損益計算書 (PL) を UI に渡すための DTO.
 */
final readonly class ProfitAndLossDto
{
    public function __construct(
        public readonly int $sales,
        public readonly int $costOfSales,
        public readonly int $grossProfit,
        public readonly int $sellingAndAdmin,
        public readonly int $operatingIncome,
        public readonly int $nonOperatingIncome,
        public readonly int $nonOperatingExpenses,
        public readonly int $ordinaryIncome,
        public readonly int $extraordinaryIncome,
        public readonly int $extraordinaryLosses,
        public readonly int $incomeBeforeTax,
        public readonly int $tax,
        public readonly int $netIncome,
    ) {
    }

    /** @return array<string, int> */
    public function toArray(): array
    {
        return [
            'sales'               => $this->sales,
            'costOfSales'         => $this->costOfSales,
            'grossProfit'         => $this->grossProfit,
            'sellingAndAdmin'     => $this->sellingAndAdmin,
            'operatingIncome'     => $this->operatingIncome,
            'nonOperatingIncome'  => $this->nonOperatingIncome,
            'nonOperatingExpenses' => $this->nonOperatingExpenses,
            'ordinaryIncome'      => $this->ordinaryIncome,
            'extraordinaryIncome' => $this->extraordinaryIncome,
            'extraordinaryLosses' => $this->extraordinaryLosses,
            'incomeBeforeTax'     => $this->incomeBeforeTax,
            'tax'                 => $this->tax,
            'netIncome'           => $this->netIncome,
        ];
    }
}
