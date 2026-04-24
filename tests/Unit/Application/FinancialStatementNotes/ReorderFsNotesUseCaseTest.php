<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatementNotes;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatementNotes\ReorderFsNotesUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteRepository;

#[CoversClass(ReorderFsNotesUseCase::class)]
final class ReorderFsNotesUseCaseTest extends TestCase
{
    public function testAppliesNewOrder(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $a = self::makeNote('01HAAAAAAAAAAAAAAAAAAAAAA1', 0);
        $b = self::makeNote('01HAAAAAAAAAAAAAAAAAAAAAA2', 1);
        $c = self::makeNote('01HAAAAAAAAAAAAAAAAAAAAAA3', 2);
        $repo->save($a);
        $repo->save($b);
        $repo->save($c);

        $uc = new ReorderFsNotesUseCase($repo, new FrozenClock());
        $updated = $uc->execute(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAE1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAE2',
            orderedIds: [$c->id, $a->id, $b->id],
        );
        self::assertSame(3, $updated);
        self::assertSame(0, $repo->findById($c->id)?->sortOrder);
        self::assertSame(1, $repo->findById($a->id)?->sortOrder);
        self::assertSame(2, $repo->findById($b->id)?->sortOrder);
    }

    public function testRejectsDuplicateIds(): void
    {
        $uc = new ReorderFsNotesUseCase(new InMemoryFsNoteRepository(), new FrozenClock());
        $this->expectException(ValidationException::class);
        $uc->execute(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAE1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAE2',
            orderedIds: ['same-id', 'same-id'],
        );
    }

    public function testSkipsNotesFromDifferentScope(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $inside = self::makeNote('01HAAAAAAAAAAAAAAAAAAAAAA1', 5);
        $outside = self::makeNote(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sortOrder: 5,
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAF0',
        );
        $repo->save($inside);
        $repo->save($outside);

        $uc = new ReorderFsNotesUseCase($repo, new FrozenClock());
        $updated = $uc->execute(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAE1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAE2',
            orderedIds: [$outside->id, $inside->id],
        );
        // Only $inside was eligible, and its index in the list is 1, so its
        // new sortOrder is 1 (was 5).
        self::assertSame(1, $updated);
        self::assertSame(1, $repo->findById($inside->id)?->sortOrder);
        // $outside is untouched.
        self::assertSame(5, $repo->findById($outside->id)?->sortOrder);
    }

    private static function makeNote(
        string $id,
        int $sortOrder,
        string $entityId = '01HAAAAAAAAAAAAAAAAAAAAAE1',
    ): FinancialStatementNote {
        $now = new DateTimeImmutable('2026-04-21T12:00:00Z');
        return new FinancialStatementNote(
            id: $id,
            entityId: $entityId,
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAE2',
            templateCode: null,
            category: FsNoteCategory::Other,
            label: 'l',
            body: 'b',
            sortOrder: $sortOrder,
            isActive: true,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
