<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement;

use Rucaro\Application\FinancialStatement\Port\GenerateFinancialStatementFromMappingUseCase;
use Rucaro\Application\FinancialStatement\Simplified\SimplifiedGenerateFinancialStatementUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinitionRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\Service\CashFlowStatementBuilder;
use Rucaro\Domain\FinancialStatement\Port\FsSectionDefinitionRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * Transition-period façade over the two FS calculation paths.
 *
 * Phase 6-A introduces a proper port of the legacy J-GAAP FS pipeline —
 * see {@see GenerateFinancialStatementFromMappingUseCase}. That path needs
 * entity-specific rows in `account_title_fs_mappings` and standard data in
 * `fs_section_definitions`. When those are present for the requested entity
 * we route through the port. When they are not (the DB is freshly seeded but
 * mappings haven't been configured yet), we fall back to the simplified
 * category-derived builder that shipped in Phase 6.6 — see
 * {@see SimplifiedGenerateFinancialStatementUseCase}.
 *
 * Existing callers (HTTP controller, tests) construct this class with just
 * the trial balance + chart-of-accounts dependencies and get the simplified
 * behaviour for free; production DI now also passes in the mapping-aware
 * port so the dispatcher has something to delegate to.
 */
final class GenerateFinancialStatementUseCase
{
    private SimplifiedGenerateFinancialStatementUseCase $simplified;

    public function __construct(
        QueryTrialBalanceUseCase $trialBalance,
        AccountTitleRepositoryInterface $accounts,
        ClockInterface $clock = new SystemClock(),
        private readonly ?AccountTitleFsMappingRepositoryInterface $mappings = null,
        private readonly ?FsSectionDefinitionRepositoryInterface $definitions = null,
        private readonly ?FinancialStatementBuilder $builder = null,
        private readonly ?AccountTitleCsMappingRepositoryInterface $csMappings = null,
        private readonly ?CsSectionDefinitionRepositoryInterface $csDefinitions = null,
        private readonly ?CashFlowStatementBuilder $csBuilder = null,
    ) {
        $this->simplified = new SimplifiedGenerateFinancialStatementUseCase(
            $trialBalance,
            $accounts,
            $clock,
        );
        $this->trialBalance = $trialBalance;
        $this->clock = $clock;
    }

    private QueryTrialBalanceUseCase $trialBalance;
    private ClockInterface $clock;

    public function execute(GenerateFinancialStatementUseCaseInput $input): FinancialStatement
    {
        if ($this->canUsePort($input->entityId)) {
            assert($this->mappings !== null);
            assert($this->definitions !== null);
            assert($this->builder !== null);
            $port = new GenerateFinancialStatementFromMappingUseCase(
                trialBalance: $this->trialBalance,
                mappings: $this->mappings,
                definitions: $this->definitions,
                builder: $this->builder,
                clock: $this->clock,
                csMappings: $this->csMappings,
                csDefinitions: $this->csDefinitions,
                csBuilder: $this->csBuilder,
            );
            return $port->execute($input);
        }
        return $this->simplified->execute($input);
    }

    private function canUsePort(string $entityId): bool
    {
        if ($this->mappings === null || $this->definitions === null || $this->builder === null) {
            return false;
        }
        return $this->mappings->findAllByEntity($entityId) !== [];
    }
}
