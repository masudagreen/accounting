<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\ConsumptionTax;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\ConsumptionTax\UpsertAccountTitleTaxDefaultsUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryAccountTitleConsumptionTaxDefaultRepository;

#[CoversClass(UpsertAccountTitleTaxDefaultsUseCase::class)]
final class UpsertAccountTitleTaxDefaultsUseCaseTest extends TestCase
{
    public function testBulkUpsertInsertsNewRows(): void
    {
        $repo = new InMemoryAccountTitleConsumptionTaxDefaultRepository();
        $useCase = new UpsertAccountTitleTaxDefaultsUseCase(
            defaults: $repo,
            ulids: new UlidGenerator(new FrozenClock()),
            clock: new FrozenClock(),
        );
        $entityId = '01HAAAAAAAAAAAAAAAAAAAAAA1';
        $out = $useCase->execute($entityId, [
            ['accountTitleId' => '01HAAAAAAAAAAAAAAAAAAAAAA2', 'categoryCode' => 'taxable_sales',    'rateCode' => 'standard_10'],
            ['accountTitleId' => '01HAAAAAAAAAAAAAAAAAAAAAA3', 'categoryCode' => 'taxable_purchase', 'rateCode' => 'standard_10'],
        ]);
        self::assertCount(2, $out);
        self::assertSame(ConsumptionTaxCategoryCode::TaxableSales, $out[0]->defaultCategoryCode);
        self::assertCount(2, $repo->findByEntity($entityId));
    }

    public function testBulkUpsertUpdatesExistingRow(): void
    {
        $repo = new InMemoryAccountTitleConsumptionTaxDefaultRepository();
        $useCase = new UpsertAccountTitleTaxDefaultsUseCase(
            defaults: $repo,
            ulids: new UlidGenerator(new FrozenClock()),
            clock: new FrozenClock(),
        );
        $entityId = '01HAAAAAAAAAAAAAAAAAAAAAA1';
        $at = '01HAAAAAAAAAAAAAAAAAAAAAA2';
        $useCase->execute($entityId, [
            ['accountTitleId' => $at, 'categoryCode' => 'taxable_sales', 'rateCode' => 'standard_10'],
        ]);
        // Retarget to non_taxable_sales.
        $out = $useCase->execute($entityId, [
            ['accountTitleId' => $at, 'categoryCode' => 'non_taxable_sales', 'rateCode' => null],
        ]);
        self::assertCount(1, $repo->findByEntity($entityId));
        self::assertSame(ConsumptionTaxCategoryCode::NonTaxableSales, $out[0]->defaultCategoryCode);
    }

    public function testRejectsUnknownCategoryCode(): void
    {
        $repo = new InMemoryAccountTitleConsumptionTaxDefaultRepository();
        $useCase = new UpsertAccountTitleTaxDefaultsUseCase(
            defaults: $repo,
            ulids: new UlidGenerator(new FrozenClock()),
            clock: new FrozenClock(),
        );
        $this->expectException(ValidationException::class);
        $useCase->execute('01HAAAAAAAAAAAAAAAAAAAAAA1', [
            ['accountTitleId' => '01HAAAAAAAAAAAAAAAAAAAAAA2', 'categoryCode' => 'bogus', 'rateCode' => null],
        ]);
    }
}
