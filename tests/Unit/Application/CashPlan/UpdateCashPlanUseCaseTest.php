<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\CashPlan;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\CashPlan\CashPlanEntryInput;
use Rucaro\Application\CashPlan\CreateCashPlanInput;
use Rucaro\Application\CashPlan\CreateCashPlanUseCase;
use Rucaro\Application\CashPlan\UpdateCashPlanInput;
use Rucaro\Application\CashPlan\UpdateCashPlanUseCase;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryCashPlanRepository;

#[CoversClass(UpdateCashPlanUseCase::class)]
final class UpdateCashPlanUseCaseTest extends TestCase
{
    public function testUpdateReplacesEntries(): void
    {
        $repo = new InMemoryCashPlanRepository();
        $ulids = new UlidGenerator(new FrozenClock());
        $create = new CreateCashPlanUseCase($repo, $ulids, new FrozenClock());
        $orig = $create->execute($this->createInput());

        $update = new UpdateCashPlanUseCase($repo, $ulids, new FrozenClock());
        $out = $update->execute(new UpdateCashPlanInput(
            id: $orig->plan->id,
            name: 'Renamed',
            entries: [
                new CashPlanEntryInput(
                    category: 'operating_in',
                    label: 'updated',
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '500.0000'),
                ),
            ],
        ));
        self::assertSame('Renamed', $out->plan->name);
        self::assertCount(1, $out->plan->entries);
        self::assertSame('updated', $out->plan->entries[0]->label);
    }

    private function createInput(): CreateCashPlanInput
    {
        return new CreateCashPlanInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            name: 'Plan',
            openingBalance: '0.0000',
            currencyCode: 'JPY',
            notes: null,
            entries: [
                new CashPlanEntryInput(
                    category: 'operating_in',
                    label: 'orig',
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '100.0000'),
                ),
            ],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAD',
        );
    }
}
