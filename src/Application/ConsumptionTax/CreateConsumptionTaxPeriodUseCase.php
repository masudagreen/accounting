<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriodRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\SimplifiedBusinessCategory;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

final readonly class CreateConsumptionTaxPeriodUseCase
{
    public function __construct(
        private ConsumptionTaxPeriodRepositoryInterface $periods,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(
        string $entityId,
        string $fiscalTermId,
        string $periodFromIso,
        string $periodToIso,
        string $method,
        ?int $simplifiedBusinessCategory,
        bool $isInterim,
    ): ConsumptionTaxPeriod {
        $now = $this->clock->getCurrentTime();
        $methodEnum = ConsumptionTaxCalculationMethod::tryFrom($method);
        if ($methodEnum === null) {
            throw ValidationException::withErrors([
                'method' => ['method must be principle/simplified/two_percent.'],
            ]);
        }
        $period = new ConsumptionTaxPeriod(
            id: $this->ulids->generate(),
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            periodFrom: new DateTimeImmutable($periodFromIso, new DateTimeZone('UTC')),
            periodTo: new DateTimeImmutable($periodToIso, new DateTimeZone('UTC')),
            calculationMethod: $methodEnum,
            simplifiedBusinessCategory: SimplifiedBusinessCategory::fromNullableInt($simplifiedBusinessCategory),
            isInterim: $isInterim,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
        $this->periods->save($period);
        return $period;
    }
}
