<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Multi;

use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Domain\FinancialStatement\FinancialStatement;

/**
 * Thin seam between the multi-period use case and whichever concrete single-
 * period implementation produces a {@see FinancialStatement}.
 *
 * In production wiring this is satisfied by
 * {@see \Rucaro\Application\FinancialStatement\Port\GenerateFinancialStatementFromMappingUseCase}
 * (or the dispatching {@see \Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase}
 * facade). Tests implement it with a trivial stub so multi-period logic can be
 * exercised without building a full trial-balance fixture per period.
 */
interface FinancialStatementProviderInterface
{
    public function provide(GenerateFinancialStatementUseCaseInput $input): FinancialStatement;
}
