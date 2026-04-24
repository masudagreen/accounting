<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FixedAsset;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FixedAsset\CreateFixedAssetInput;
use Rucaro\Application\FixedAsset\CreateFixedAssetUseCase;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleInput;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleUseCase;
use Rucaro\Domain\FixedAsset\Service\DepreciationCalculatorFactory;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryDepreciationScheduleRepository;
use Rucaro\Tests\Support\Fake\InMemoryFixedAssetRepository;

#[CoversClass(GenerateDepreciationScheduleUseCase::class)]
final class GenerateDepreciationScheduleUseCaseTest extends TestCase
{
    public function testGeneratesFirstPeriodScheduleForEntity(): void
    {
        $assets = new InMemoryFixedAssetRepository();
        $schedules = new InMemoryDepreciationScheduleRepository();
        $ulids = new UlidGenerator(new FrozenClock());

        // create asset
        (new CreateFixedAssetUseCase($assets, $ulids, new FrozenClock()))->execute(
            new CreateFixedAssetInput(
                entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
                assetCode: 'M-001',
                assetName: 'Test Machine',
                categoryCode: 'machinery',
                assetAccountTitleId: null,
                accumulatedDepreciationAccountTitleId: null,
                depreciationExpenseAccountTitleId: null,
                acquisitionDate: new DateTimeImmutable('2025-04-01'),
                serviceStartDate: new DateTimeImmutable('2025-04-01'),
                acquisitionCost: '1000000.0000',
                residualValue: '0.0000',
                usefulLifeYears: 10,
                method: 'straight_line',
                quantity: 1,
                departmentCode: null,
                note: null,
                createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            ),
        );

        $uc = new GenerateDepreciationScheduleUseCase(
            assets: $assets,
            schedules: $schedules,
            calculatorFactory: new DepreciationCalculatorFactory(),
            ulids: $ulids,
            clock: new FrozenClock(),
        );
        $out = $uc->execute(new GenerateDepreciationScheduleInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            fiscalTermId: '01HBBBBBBBBBBBBBBBBBBBBBBB',
            fiscalTermStart: new DateTimeImmutable('2025-04-01'),
            fiscalTermEnd: new DateTimeImmutable('2026-03-31'),
        ));
        self::assertCount(1, $out->entries);
        self::assertSame('100000.0000', $out->entries[0]->depreciationAmount);
        self::assertSame('900000.0000', $out->entries[0]->closingBookValue);
        self::assertSame(1, $out->entries[0]->periodNumber);
    }

    public function testRegenerationOverwritesUnpostedEntriesButPreservesPosted(): void
    {
        $assets = new InMemoryFixedAssetRepository();
        $schedules = new InMemoryDepreciationScheduleRepository();
        $ulids = new UlidGenerator(new FrozenClock());

        (new CreateFixedAssetUseCase($assets, $ulids, new FrozenClock()))->execute(
            new CreateFixedAssetInput(
                entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
                assetCode: 'M-002',
                assetName: 'Machine 2',
                categoryCode: 'machinery',
                assetAccountTitleId: null,
                accumulatedDepreciationAccountTitleId: null,
                depreciationExpenseAccountTitleId: null,
                acquisitionDate: new DateTimeImmutable('2025-04-01'),
                serviceStartDate: new DateTimeImmutable('2025-04-01'),
                acquisitionCost: '600000.0000',
                residualValue: '0.0000',
                usefulLifeYears: 6,
                method: 'straight_line',
                quantity: 1,
                departmentCode: null,
                note: null,
                createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            ),
        );

        $uc = new GenerateDepreciationScheduleUseCase(
            assets: $assets,
            schedules: $schedules,
            calculatorFactory: new DepreciationCalculatorFactory(),
            ulids: $ulids,
            clock: new FrozenClock(),
        );
        $out1 = $uc->execute(new GenerateDepreciationScheduleInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            fiscalTermId: '01HBBBBBBBBBBBBBBBBBBBBBBB',
            fiscalTermStart: new DateTimeImmutable('2025-04-01'),
            fiscalTermEnd: new DateTimeImmutable('2026-03-31'),
        ));
        $firstId = $out1->entries[0]->id;
        // Re-run should reuse the same row.
        $out2 = $uc->execute(new GenerateDepreciationScheduleInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            fiscalTermId: '01HBBBBBBBBBBBBBBBBBBBBBBB',
            fiscalTermStart: new DateTimeImmutable('2025-04-01'),
            fiscalTermEnd: new DateTimeImmutable('2026-03-31'),
        ));
        self::assertSame($firstId, $out2->entries[0]->id);
    }
}
