<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Budget;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Budget\BudgetLineItemInput;
use Rucaro\Application\Budget\CreateBudgetInput;
use Rucaro\Application\Budget\CreateBudgetUseCase;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBudgetRepository;

#[CoversClass(CreateBudgetUseCase::class)]
final class CreateBudgetUseCaseTest extends TestCase
{
    public function testCreatesDraftBudgetAndPersists(): void
    {
        $repo = new InMemoryBudgetRepository();
        $uc = new CreateBudgetUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $out = $uc->execute(self::validInput());
        self::assertSame(BudgetStatus::Draft, $out->budget->status);
        self::assertSame('Plan 2026', $out->budget->name);
        self::assertCount(2, $out->budget->lineItems);
        self::assertNotNull($repo->findById($out->budget->id));
    }

    public function testRejectsDuplicateName(): void
    {
        $repo = new InMemoryBudgetRepository();
        $uc = new CreateBudgetUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());
        $uc->execute(self::validInput());
        $this->expectException(ValidationException::class);
        $uc->execute(self::validInput());
    }

    public function testRejectsNonUlidAccountTitle(): void
    {
        $repo = new InMemoryBudgetRepository();
        $uc = new CreateBudgetUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());
        $input = new CreateBudgetInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            name: 'bad',
            notes: null,
            lineItems: [
                new BudgetLineItemInput(
                    accountTitleId: 'not-a-ulid',
                    subAccountTitleId: null,
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '0.0000'),
                ),
            ],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAA3',
        );
        $this->expectException(ValidationException::class);
        $uc->execute($input);
    }

    private static function validInput(): CreateBudgetInput
    {
        return new CreateBudgetInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            name: 'Plan 2026',
            notes: null,
            lineItems: [
                new BudgetLineItemInput(
                    accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC0',
                    subAccountTitleId: null,
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '1500000.0000'),
                ),
                new BudgetLineItemInput(
                    accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC1',
                    subAccountTitleId: null,
                    sortOrder: 1,
                    monthlyAmounts: array_fill(0, 12, '300000.0000'),
                ),
            ],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAA3',
        );
    }
}
