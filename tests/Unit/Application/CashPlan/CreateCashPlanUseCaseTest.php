<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\CashPlan;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\CashPlan\CashPlanEntryInput;
use Rucaro\Application\CashPlan\CreateCashPlanInput;
use Rucaro\Application\CashPlan\CreateCashPlanUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryCashPlanRepository;

#[CoversClass(CreateCashPlanUseCase::class)]
final class CreateCashPlanUseCaseTest extends TestCase
{
    public function testCreatesPlanAndPersistsIt(): void
    {
        $repo = new InMemoryCashPlanRepository();
        $ulids = new UlidGenerator(new FrozenClock());
        $uc = new CreateCashPlanUseCase($repo, $ulids, new FrozenClock());

        $out = $uc->execute(self::validInput());
        self::assertNotNull($repo->findById($out->plan->id));
        self::assertSame('Q1 Plan', $out->plan->name);
        self::assertCount(2, $out->plan->entries);
    }

    public function testRejectsDuplicateName(): void
    {
        $repo = new InMemoryCashPlanRepository();
        $ulids = new UlidGenerator(new FrozenClock());
        $uc = new CreateCashPlanUseCase($repo, $ulids, new FrozenClock());
        $uc->execute(self::validInput());
        $this->expectException(ValidationException::class);
        $uc->execute(self::validInput());
    }

    public function testRejectsUnknownCategory(): void
    {
        $repo = new InMemoryCashPlanRepository();
        $ulids = new UlidGenerator(new FrozenClock());
        $uc = new CreateCashPlanUseCase($repo, $ulids, new FrozenClock());
        $input = new CreateCashPlanInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            name: 'bad',
            openingBalance: '0.0000',
            currencyCode: 'JPY',
            notes: null,
            entries: [
                new CashPlanEntryInput(
                    category: 'made_up',
                    label: 'foo',
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '0.0000'),
                ),
            ],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAD',
        );
        $this->expectException(ValidationException::class);
        $uc->execute($input);
    }

    private static function validInput(): CreateCashPlanInput
    {
        $zeroes = array_fill(0, 12, '0.0000');
        $sales = $zeroes;
        $sales[0] = '1000000.0000';
        $salaries = $zeroes;
        $salaries[0] = '300000.0000';
        return new CreateCashPlanInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            name: 'Q1 Plan',
            openingBalance: '500000.0000',
            currencyCode: 'JPY',
            notes: null,
            entries: [
                new CashPlanEntryInput(
                    category: 'operating_in',
                    label: '売上入金',
                    sortOrder: 0,
                    monthlyAmounts: $sales,
                ),
                new CashPlanEntryInput(
                    category: 'operating_out',
                    label: '給与',
                    sortOrder: 1,
                    monthlyAmounts: $salaries,
                ),
            ],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAD',
        );
    }
}
