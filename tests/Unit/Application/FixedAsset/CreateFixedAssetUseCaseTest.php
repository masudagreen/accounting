<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FixedAsset;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FixedAsset\CreateFixedAssetInput;
use Rucaro\Application\FixedAsset\CreateFixedAssetUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryFixedAssetRepository;

#[CoversClass(CreateFixedAssetUseCase::class)]
final class CreateFixedAssetUseCaseTest extends TestCase
{
    public function testCreatesAssetAndPersistsIt(): void
    {
        $repo = new InMemoryFixedAssetRepository();
        $ulids = new UlidGenerator(new FrozenClock());
        $uc = new CreateFixedAssetUseCase($repo, $ulids, new FrozenClock());
        $out = $uc->execute(self::validInput());
        self::assertNotNull($repo->findById($out->asset->id));
        self::assertSame('M-001', $out->asset->assetCode);
    }

    public function testRejectsDuplicateAssetCode(): void
    {
        $repo = new InMemoryFixedAssetRepository();
        $ulids = new UlidGenerator(new FrozenClock());
        $uc = new CreateFixedAssetUseCase($repo, $ulids, new FrozenClock());
        $uc->execute(self::validInput());
        $this->expectException(ValidationException::class);
        $uc->execute(self::validInput());
    }

    public function testRejectsUnknownMethod(): void
    {
        $repo = new InMemoryFixedAssetRepository();
        $ulids = new UlidGenerator(new FrozenClock());
        $uc = new CreateFixedAssetUseCase($repo, $ulids, new FrozenClock());
        $input = new CreateFixedAssetInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            assetCode: 'X-01',
            assetName: 'x',
            categoryCode: 'other',
            assetAccountTitleId: null,
            accumulatedDepreciationAccountTitleId: null,
            depreciationExpenseAccountTitleId: null,
            acquisitionDate: new DateTimeImmutable('2025-04-01'),
            serviceStartDate: new DateTimeImmutable('2025-04-01'),
            acquisitionCost: '100000.0000',
            residualValue: '0.0000',
            usefulLifeYears: 5,
            method: 'made_up_method',
            quantity: 1,
            departmentCode: null,
            note: null,
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAC',
        );
        $this->expectException(ValidationException::class);
        $uc->execute($input);
    }

    private static function validInput(): CreateFixedAssetInput
    {
        return new CreateFixedAssetInput(
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
        );
    }
}
