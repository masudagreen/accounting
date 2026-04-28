<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * 貸借対照表 (BS) を UI に渡すための DTO.
 */
final readonly class BalanceSheetDto
{
    public function __construct(
        public readonly int $totalAssets,
        public readonly int $totalLiabilities,
        public readonly int $totalEquity,
    ) {
    }

    /** @return array<string, int> */
    public function toArray(): array
    {
        return [
            'totalAssets'      => $this->totalAssets,
            'totalLiabilities' => $this->totalLiabilities,
            'totalEquity'      => $this->totalEquity,
        ];
    }
}
