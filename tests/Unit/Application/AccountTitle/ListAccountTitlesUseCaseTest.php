<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\AccountTitle;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Tests\Unit\Application\Support\InMemoryAccountTitleRepo;

#[CoversClass(ListAccountTitlesUseCase::class)]
final class ListAccountTitlesUseCaseTest extends TestCase
{
    public function testListsByEntityAndCategory(): void
    {
        $repo = new InMemoryAccountTitleRepo();
        $repo->add($this->title('01HW7K9B2QV7C8Y4ZACCTTL001', 'ENT1', '100', 'Cash', 'asset'));
        $repo->add($this->title('01HW7K9B2QV7C8Y4ZACCTTL002', 'ENT1', '200', 'Sales', 'revenue'));
        $repo->add($this->title('01HW7K9B2QV7C8Y4ZACCTTL003', 'ENT2', '100', 'Cash', 'asset'));

        $useCase = new ListAccountTitlesUseCase($repo);

        $out = $useCase->execute(new ListAccountTitlesUseCaseInput(
            entityId: 'ENT1',
            page: 1,
            pageSize: 50,
            category: 'asset',
        ));

        self::assertSame(1, $out->total);
        self::assertSame('Cash', $out->items[0]->name);
    }

    public function testSearchMatchesCodeOrName(): void
    {
        $repo = new InMemoryAccountTitleRepo();
        $repo->add($this->title('01HW7K9B2QV7C8Y4ZACCTTL001', 'ENT1', '101', 'Cash', 'asset'));
        $repo->add($this->title('01HW7K9B2QV7C8Y4ZACCTTL002', 'ENT1', '250', 'Sales', 'revenue'));

        $useCase = new ListAccountTitlesUseCase($repo);

        self::assertSame(
            1,
            $useCase->execute(new ListAccountTitlesUseCaseInput('ENT1', 1, 50, search: 'Sal'))->total,
        );
        self::assertSame(
            1,
            $useCase->execute(new ListAccountTitlesUseCaseInput('ENT1', 1, 50, search: '101'))->total,
        );
    }

    private function title(string $id, string $entityId, string $code, string $name, string $category): AccountTitle
    {
        return new AccountTitle(
            id: $id,
            entityId: $entityId,
            code: $code,
            name: $name,
            category: $category,
            normalSide: $category === 'asset' || $category === 'expense' ? 'debit' : 'credit',
            parentId: null,
            sortOrder: 0,
            isActive: true,
            createdAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
        );
    }
}
