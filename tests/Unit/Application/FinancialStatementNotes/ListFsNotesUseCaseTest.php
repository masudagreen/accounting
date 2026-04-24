<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatementNotes;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatementNotes\GetFsNoteUseCase;
use Rucaro\Application\FinancialStatementNotes\ListFsNotesUseCase;
use Rucaro\Application\FinancialStatementNotes\ListFsNoteTemplatesUseCase;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplate;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteRepository;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteTemplateRepository;

#[CoversClass(ListFsNotesUseCase::class)]
#[CoversClass(GetFsNoteUseCase::class)]
#[CoversClass(ListFsNoteTemplatesUseCase::class)]
final class ListFsNotesUseCaseTest extends TestCase
{
    public function testListFiltersByScopeAndActive(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $active = self::makeNote('01HAAAAAAAAAAAAAAAAAAAAAA1', 0, true);
        $inactive = self::makeNote('01HAAAAAAAAAAAAAAAAAAAAAA2', 1, false);
        $repo->save($active);
        $repo->save($inactive);

        $all = (new ListFsNotesUseCase($repo))->execute(
            '01HAAAAAAAAAAAAAAAAAAAAAE1',
            '01HAAAAAAAAAAAAAAAAAAAAAE2',
            false,
        );
        $onlyActive = (new ListFsNotesUseCase($repo))->execute(
            '01HAAAAAAAAAAAAAAAAAAAAAE1',
            '01HAAAAAAAAAAAAAAAAAAAAAE2',
            true,
        );
        self::assertCount(2, $all);
        self::assertCount(1, $onlyActive);
        self::assertTrue($onlyActive[0]->isActive);
    }

    public function testGetReturnsNullWhenMissing(): void
    {
        $uc = new GetFsNoteUseCase(new InMemoryFsNoteRepository());
        self::assertNull($uc->execute('01HAAAAAAAAAAAAAAAAAAAAAZZ'));
    }

    public function testListTemplatesReturnsAll(): void
    {
        $repo = new InMemoryFsNoteTemplateRepository();
        $repo->add(new FsNoteTemplate(
            id: '01HAAAAAAAAAAAAAAAAAAAAATT1',
            code: 'X',
            category: FsNoteCategory::Other,
            label: 'x',
            defaultBody: 'y',
            sortOrder: 0,
        ));
        $uc = new ListFsNoteTemplatesUseCase($repo);
        self::assertCount(1, $uc->execute());
    }

    private static function makeNote(string $id, int $sortOrder, bool $active): FinancialStatementNote
    {
        $now = new DateTimeImmutable('2026-04-21T12:00:00Z');
        return new FinancialStatementNote(
            id: $id,
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAE1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAE2',
            templateCode: null,
            category: FsNoteCategory::Other,
            label: 'label ' . $id,
            body: 'body',
            sortOrder: $sortOrder,
            isActive: $active,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
