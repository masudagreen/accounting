<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnFormType;

/**
 * Input for {@see GenerateBlueReturnSnapshotUseCase}.
 *
 * The use case is a pure derivation: callers pass in
 * already-classified trial-balance buckets + monthly aggregates and
 * receive a {@see \Rucaro\Domain\BlueReturn\BlueReturnSnapshot}. We
 * keep the classification step outside the use case so the domain
 * never grows a chart-of-accounts dependency.
 */
final readonly class GenerateBlueReturnSnapshotInput
{
    /**
     * @param array<string, string>                                              $revenueByAccount
     * @param array<string, string>                                              $costOfSalesByAccount
     * @param array<string, string>                                              $expensesByAccount
     * @param list<array{month:int,sales:string,purchase:string,salary:string}>  $monthlyRows
     * @param array<string, list<array<string, mixed>>>                          $breakdown
     * @param array<string, string>                                              $assetsByAccount
     * @param array<string, string>                                              $liabilitiesByAccount
     * @param array<string, string>                                              $equityByAccount
     */
    public function __construct(
        public BlueReturnFormType $formType,
        public array $revenueByAccount,
        public array $costOfSalesByAccount,
        public array $expensesByAccount,
        public array $monthlyRows,
        public array $breakdown,
        public array $assetsByAccount,
        public array $liabilitiesByAccount,
        public array $equityByAccount,
    ) {
    }
}
