<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\ConsumptionTax;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\ConsumptionTax\CreateConsumptionTaxPeriodUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryConsumptionTaxPeriodRepository;

#[CoversClass(CreateConsumptionTaxPeriodUseCase::class)]
final class CreateConsumptionTaxPeriodUseCaseTest extends TestCase
{
    public function testSavesPrinciplePeriod(): void
    {
        $repo = new InMemoryConsumptionTaxPeriodRepository();
        $useCase = new CreateConsumptionTaxPeriodUseCase(
            periods: $repo,
            ulids: new UlidGenerator(new FrozenClock()),
            clock: new FrozenClock(),
        );
        $period = $useCase->execute(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            periodFromIso: '2026-04-01',
            periodToIso: '2027-03-31',
            method: 'principle',
            simplifiedBusinessCategory: null,
            isInterim: false,
        );
        self::assertSame(ConsumptionTaxCalculationMethod::Principle, $period->calculationMethod);
        self::assertNotNull($repo->findById($period->id));
    }

    public function testSavesSimplifiedPeriodWithBusinessCategory(): void
    {
        $repo = new InMemoryConsumptionTaxPeriodRepository();
        $useCase = new CreateConsumptionTaxPeriodUseCase(
            periods: $repo,
            ulids: new UlidGenerator(new FrozenClock()),
            clock: new FrozenClock(),
        );
        $period = $useCase->execute(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            periodFromIso: '2026-04-01',
            periodToIso: '2027-03-31',
            method: 'simplified',
            simplifiedBusinessCategory: 1,
            isInterim: false,
        );
        self::assertSame(ConsumptionTaxCalculationMethod::Simplified, $period->calculationMethod);
        self::assertNotNull($period->simplifiedBusinessCategory);
    }

    public function testRejectsUnknownMethod(): void
    {
        $useCase = new CreateConsumptionTaxPeriodUseCase(
            periods: new InMemoryConsumptionTaxPeriodRepository(),
            ulids: new UlidGenerator(new FrozenClock()),
            clock: new FrozenClock(),
        );
        $this->expectException(ValidationException::class);
        $useCase->execute(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            periodFromIso: '2026-04-01',
            periodToIso: '2027-03-31',
            method: 'bogus_method',
            simplifiedBusinessCategory: null,
            isInterim: false,
        );
    }
}
