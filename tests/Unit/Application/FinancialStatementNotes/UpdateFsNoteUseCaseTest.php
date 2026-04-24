<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatementNotes;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatementNotes\UpdateFsNoteInput;
use Rucaro\Application\FinancialStatementNotes\UpdateFsNoteUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteRepository;

#[CoversClass(UpdateFsNoteUseCase::class)]
final class UpdateFsNoteUseCaseTest extends TestCase
{
    public function testPatchesLabelOnly(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $seed = self::makeNote();
        $repo->save($seed);

        $uc = new UpdateFsNoteUseCase($repo, new FrozenClock('2026-05-01T00:00:00Z'));
        $out = $uc->execute(new UpdateFsNoteInput(id: $seed->id, label: 'updated label'));

        self::assertSame('updated label', $out->note->label);
        self::assertSame('original body', $out->note->body);
        self::assertSame(FsNoteCategory::AccountingPolicy, $out->note->category);
    }

    public function testMissingIdRaisesValidation(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $uc = new UpdateFsNoteUseCase($repo, new FrozenClock());
        $this->expectException(ValidationException::class);
        $uc->execute(new UpdateFsNoteInput(id: '01HAAAAAAAAAAAAAAAAAAAAAZZ', label: 'x'));
    }

    public function testUnknownCategoryRejected(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $seed = self::makeNote();
        $repo->save($seed);
        $uc = new UpdateFsNoteUseCase($repo, new FrozenClock());
        $this->expectException(ValidationException::class);
        $uc->execute(new UpdateFsNoteInput(id: $seed->id, category: 'nope'));
    }

    public function testCanToggleActive(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $seed = self::makeNote();
        $repo->save($seed);

        $uc = new UpdateFsNoteUseCase($repo, new FrozenClock());
        $out = $uc->execute(new UpdateFsNoteInput(id: $seed->id, isActive: false));
        self::assertFalse($out->note->isActive);
    }

    private static function makeNote(): FinancialStatementNote
    {
        $now = new DateTimeImmutable('2026-04-21T12:00:00Z');
        return new FinancialStatementNote(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA9',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            templateCode: null,
            category: FsNoteCategory::AccountingPolicy,
            label: 'original label',
            body: 'original body',
            sortOrder: 0,
            isActive: true,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
