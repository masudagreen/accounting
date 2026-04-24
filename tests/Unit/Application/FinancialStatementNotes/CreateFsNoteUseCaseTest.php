<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatementNotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatementNotes\CreateFsNoteInput;
use Rucaro\Application\FinancialStatementNotes\CreateFsNoteUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteRepository;

#[CoversClass(CreateFsNoteUseCase::class)]
final class CreateFsNoteUseCaseTest extends TestCase
{
    public function testPersistsNewNote(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $uc = new CreateFsNoteUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $out = $uc->execute(self::validInput());
        self::assertSame('棚卸資産の評価方法', $out->note->label);
        self::assertSame(FsNoteCategory::AccountingPolicy, $out->note->category);
        self::assertNotNull($repo->findById($out->note->id));
    }

    public function testRejectsUnknownCategory(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $uc = new CreateFsNoteUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $this->expectException(ValidationException::class);
        $uc->execute(new CreateFsNoteInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            category: 'not_a_category',
            label: 'x',
            body: 'y',
        ));
    }

    public function testRejectsNonUlidEntityId(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $uc = new CreateFsNoteUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $this->expectException(\InvalidArgumentException::class);
        $uc->execute(new CreateFsNoteInput(
            entityId: 'bad',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            category: 'other',
            label: 'x',
            body: 'y',
        ));
    }

    private static function validInput(): CreateFsNoteInput
    {
        return new CreateFsNoteInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            category: 'accounting_policy',
            label: '棚卸資産の評価方法',
            body: '棚卸資産の評価は総平均法による原価法によっております。',
            templateCode: 'AP_INVENTORY',
            sortOrder: 10,
            isActive: true,
        );
    }
}
