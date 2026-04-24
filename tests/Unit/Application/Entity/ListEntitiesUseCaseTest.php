<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCaseInput;
use Rucaro\Domain\Entity\Entity;
use Rucaro\Tests\Unit\Application\Support\InMemoryEntityRepo;

#[CoversClass(ListEntitiesUseCase::class)]
final class ListEntitiesUseCaseTest extends TestCase
{
    public function testListsOnlyOwnedEntitiesWithPagination(): void
    {
        $repo = new InMemoryEntityRepo();
        $repo->add($this->entity('01HW7K9B2QV7C8Y4ZENTITY0001', 'owner1', 'Cafe A'));
        $repo->add($this->entity('01HW7K9B2QV7C8Y4ZENTITY0002', 'owner1', 'Bar B'));
        $repo->add($this->entity('01HW7K9B2QV7C8Y4ZENTITY0003', 'owner1', 'Cafe C'));
        $repo->add($this->entity('01HW7K9B2QV7C8Y4ZENTITY0009', 'other', 'Other Inc'));

        $useCase = new ListEntitiesUseCase($repo);

        $out = $useCase->execute(new ListEntitiesUseCaseInput(
            ownerUserId: 'owner1',
            page: 1,
            pageSize: 2,
        ));

        self::assertSame(3, $out->total);
        self::assertCount(2, $out->items);
        self::assertSame(1, $out->page);
        self::assertSame(2, $out->pageSize);
    }

    public function testSearchFilterNarrowsResults(): void
    {
        $repo = new InMemoryEntityRepo();
        $repo->add($this->entity('01HW7K9B2QV7C8Y4ZENTITY0001', 'owner1', 'Cafe Alpha'));
        $repo->add($this->entity('01HW7K9B2QV7C8Y4ZENTITY0002', 'owner1', 'Bar Beta'));

        $useCase = new ListEntitiesUseCase($repo);

        $out = $useCase->execute(new ListEntitiesUseCaseInput(
            ownerUserId: 'owner1',
            page: 1,
            pageSize: 50,
            search: 'Cafe',
        ));

        self::assertSame(1, $out->total);
        self::assertSame('Cafe Alpha', $out->items[0]->name);
    }

    private function entity(string $id, string $owner, string $name): Entity
    {
        return new Entity(
            id: $id,
            ownerUserId: $owner,
            name: $name,
            nationCode: 'JPN',
            currencyCode: 'JPY',
            fiscalStartMmDd: '0401',
            isActive: true,
            createdAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
        );
    }
}
