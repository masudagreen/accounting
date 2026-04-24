<?php

declare(strict_types=1);

namespace Rucaro\Application\BreakEvenPoint;

use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassificationRepositoryInterface;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointAnalysis;
use Rucaro\Domain\BreakEvenPoint\Service\BreakEvenPointCalculator;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Orchestrates a Break-Even Point analysis.
 *
 * Steps:
 *   1. Pull a TrialBalance for the requested period (via the existing
 *      {@see QueryTrialBalanceUseCase}, so snapshot + tail logic is reused).
 *   2. Load the per-entity CVP classification map.
 *   3. Delegate the numbers to {@see BreakEvenPointCalculator}.
 */
final readonly class AnalyzeBreakEvenPointUseCase
{
    public function __construct(
        private QueryTrialBalanceUseCase $trialBalance,
        private AccountTitleCvpClassificationRepositoryInterface $classifications,
        private BreakEvenPointCalculator $calculator,
        private ClockInterface $clock,
    ) {
    }

    public function execute(AnalyzeBreakEvenPointInput $input): BreakEvenPointAnalysis
    {
        if (!UlidGenerator::isValid($input->entityId)) {
            throw new \InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->fiscalTermId)) {
            throw new \InvalidArgumentException('fiscalTermId must be a ULID.');
        }

        $tb = $this->trialBalance->execute(new QueryTrialBalanceUseCaseInput(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            fiscalTermStartDate: $input->fromDate,
            asOf: $input->toDate,
            currencyCode: $input->currencyCode,
        ));

        $classifications = $this->classifications->findAllByEntity($input->entityId);

        return $this->calculator->calculate(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            fromDate: $input->fromDate,
            toDate: $input->toDate,
            currencyCode: $input->currencyCode,
            trialBalance: $tb,
            classifications: $classifications,
            generatedAt: $this->clock->getCurrentTime(),
        );
    }
}
