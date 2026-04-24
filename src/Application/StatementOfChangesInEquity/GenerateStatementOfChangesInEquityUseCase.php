<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use InvalidArgumentException;
use Rucaro\Domain\StatementOfChangesInEquity\Service\StatementOfChangesInEquityBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquity;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Assemble a {@see StatementOfChangesInEquity} read model from the
 * persisted {@see \Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment}
 * rows plus caller-supplied opening balances and net income.
 *
 * The builder itself is pure; this UseCase owns the "compose over
 * the repository + clock" wiring.
 */
final readonly class GenerateStatementOfChangesInEquityUseCase
{
    public function __construct(
        private SsManualAdjustmentRepositoryInterface $repo,
        private StatementOfChangesInEquityBuilder $builder,
        private ClockInterface $clock,
    ) {
    }

    public function execute(GenerateStatementOfChangesInEquityInput $input): StatementOfChangesInEquity
    {
        if (!UlidGenerator::isValid($input->entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->fiscalTermId)) {
            throw new InvalidArgumentException('fiscalTermId must be a ULID.');
        }
        if ($input->toDate < $input->fromDate) {
            throw new InvalidArgumentException('toDate must be on or after fromDate.');
        }

        $adjustments = $this->repo->findByEntityAndFiscalTerm(
            $input->entityId,
            $input->fiscalTermId,
        );

        return $this->builder->build(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            fromDate: $input->fromDate,
            toDate: $input->toDate,
            currencyCode: $input->currencyCode,
            openingBalances: $input->openingBalances,
            adjustments: $adjustments,
            netIncome: $input->netIncome,
            generatedAt: $this->clock->getCurrentTime(),
        );
    }
}
