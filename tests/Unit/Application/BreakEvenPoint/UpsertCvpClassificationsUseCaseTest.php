<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\BreakEvenPoint;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\BreakEvenPoint\UpsertCvpClassificationInput;
use Rucaro\Application\BreakEvenPoint\UpsertCvpClassificationsUseCase;
use Rucaro\Domain\BreakEvenPoint\CvpCostType;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Tests\Support\Fake\InMemoryCvpClassificationRepository;

#[CoversClass(UpsertCvpClassificationsUseCase::class)]
final class UpsertCvpClassificationsUseCaseTest extends TestCase
{
    public function testBulkUpsertCanonicalizesRatios(): void
    {
        $repo = new InMemoryCvpClassificationRepository();
        $uc = new UpsertCvpClassificationsUseCase($repo);
        $entity = '01HAAAAAAAAAAAAAAAAAAAAAAA';
        $out = $uc->execute($entity, [
            new UpsertCvpClassificationInput(
                accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
                costType: 'variable',
                variableRatio: '0.7000',
            ),
            new UpsertCvpClassificationInput(
                accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAAC',
                costType: 'fixed',
                variableRatio: '0.9000',
            ),
            new UpsertCvpClassificationInput(
                accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAAD',
                costType: 'semi_variable',
                variableRatio: '0.4000',
            ),
        ]);
        self::assertCount(3, $out);
        self::assertSame(CvpCostType::Variable, $out[0]->costType);
        self::assertSame('1.0000', $out[0]->variableRatio);
        self::assertSame('0.0000', $out[1]->variableRatio);
        self::assertSame('0.4000', $out[2]->variableRatio);
        self::assertCount(3, $repo->findAllByEntity($entity));
    }

    public function testRejectsUnknownCostType(): void
    {
        $repo = new InMemoryCvpClassificationRepository();
        $uc = new UpsertCvpClassificationsUseCase($repo);
        $this->expectException(ValidationException::class);
        $uc->execute('01HAAAAAAAAAAAAAAAAAAAAAAA', [
            new UpsertCvpClassificationInput(
                accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
                costType: 'banana',
            ),
        ]);
    }

    public function testRejectsInvalidEntityUlid(): void
    {
        $repo = new InMemoryCvpClassificationRepository();
        $uc = new UpsertCvpClassificationsUseCase($repo);
        $this->expectException(\InvalidArgumentException::class);
        $uc->execute('not-a-ulid', []);
    }
}
