<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatementNotes;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatementNotes\DeleteFsNoteUseCase;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteRepository;

#[CoversClass(DeleteFsNoteUseCase::class)]
final class DeleteFsNoteUseCaseTest extends TestCase
{
    public function testDeletesExisting(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $seed = self::makeNote();
        $repo->save($seed);
        $uc = new DeleteFsNoteUseCase($repo);
        $uc->execute($seed->id);
        self::assertNull($repo->findById($seed->id));
    }

    public function testMissingIsNoOp(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $uc = new DeleteFsNoteUseCase($repo);
        $uc->execute('01HAAAAAAAAAAAAAAAAAAAAZZZ');
        $this->expectNotToPerformAssertions();
    }

    private static function makeNote(): FinancialStatementNote
    {
        $now = new DateTimeImmutable('2026-04-21T12:00:00Z');
        return new FinancialStatementNote(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            templateCode: null,
            category: FsNoteCategory::Other,
            label: 'x',
            body: 'y',
            sortOrder: 0,
            isActive: true,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
