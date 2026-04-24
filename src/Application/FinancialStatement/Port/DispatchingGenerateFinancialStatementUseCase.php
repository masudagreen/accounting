<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Port;

use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase as SimplifiedUseCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMappingRepositoryInterface;

/**
 * Transition-period use case that routes the request to the proper port
 * implementation when the entity has FS mappings configured, and falls
 * back to the simplified category-based path otherwise.
 *
 * Lets entities migrate to the explicit mapping model one at a time without
 * a breaking-change moment. Once every entity seed is migrated, callers
 * should switch the DI binding directly to
 * {@see GenerateFinancialStatementFromMappingUseCase} and retire this
 * dispatcher in Wave 6-C cleanup.
 */
final readonly class DispatchingGenerateFinancialStatementUseCase
{
    public function __construct(
        private GenerateFinancialStatementFromMappingUseCase $port,
        private SimplifiedUseCase $simplified,
        private AccountTitleFsMappingRepositoryInterface $mappings,
    ) {
    }

    public function execute(GenerateFinancialStatementUseCaseInput $input): FinancialStatement
    {
        $entityMappings = $this->mappings->findAllByEntity($input->entityId);
        if ($entityMappings === []) {
            return $this->simplified->execute($input);
        }
        return $this->port->execute($input);
    }
}
