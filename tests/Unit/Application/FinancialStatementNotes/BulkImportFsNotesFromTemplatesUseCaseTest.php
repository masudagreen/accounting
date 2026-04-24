<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatementNotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatementNotes\BulkImportFsNotesFromTemplatesInput;
use Rucaro\Application\FinancialStatementNotes\BulkImportFsNotesFromTemplatesUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplate;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteRepository;
use Rucaro\Tests\Support\Fake\InMemoryFsNoteTemplateRepository;

#[CoversClass(BulkImportFsNotesFromTemplatesUseCase::class)]
final class BulkImportFsNotesFromTemplatesUseCaseTest extends TestCase
{
    public function testInsertsSelectedTemplates(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $tplRepo = $this->seedTemplates();
        $uc = new BulkImportFsNotesFromTemplatesUseCase(
            $repo,
            $tplRepo,
            new UlidGenerator(new FrozenClock()),
            new FrozenClock(),
        );

        $out = $uc->execute(new BulkImportFsNotesFromTemplatesInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            templateCodes: ['AP_INVENTORY', 'AP_DEPRECIATION'],
        ));
        self::assertCount(2, $out);
        self::assertSame('棚卸資産の評価方法', $out[0]->label);
    }

    public function testIsIdempotentOnRepeatedImport(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $tplRepo = $this->seedTemplates();
        $uc = new BulkImportFsNotesFromTemplatesUseCase(
            $repo,
            $tplRepo,
            new UlidGenerator(new FrozenClock()),
            new FrozenClock(),
        );
        $input = new BulkImportFsNotesFromTemplatesInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            templateCodes: ['AP_INVENTORY'],
        );

        $first = $uc->execute($input);
        $second = $uc->execute($input);
        self::assertCount(1, $first);
        self::assertCount(0, $second);
        self::assertCount(1, $repo->findByEntityAndTerm(
            '01HAAAAAAAAAAAAAAAAAAAAAA1',
            '01HAAAAAAAAAAAAAAAAAAAAAA2',
        ));
    }

    public function testEmptyCodesRejected(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $tplRepo = $this->seedTemplates();
        $uc = new BulkImportFsNotesFromTemplatesUseCase(
            $repo,
            $tplRepo,
            new UlidGenerator(new FrozenClock()),
            new FrozenClock(),
        );
        $this->expectException(ValidationException::class);
        $uc->execute(new BulkImportFsNotesFromTemplatesInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            templateCodes: [],
        ));
    }

    public function testUnknownCodesRejected(): void
    {
        $repo = new InMemoryFsNoteRepository();
        $tplRepo = $this->seedTemplates();
        $uc = new BulkImportFsNotesFromTemplatesUseCase(
            $repo,
            $tplRepo,
            new UlidGenerator(new FrozenClock()),
            new FrozenClock(),
        );
        $this->expectException(ValidationException::class);
        $uc->execute(new BulkImportFsNotesFromTemplatesInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            templateCodes: ['NOPE_1', 'NOPE_2'],
        ));
    }

    private function seedTemplates(): InMemoryFsNoteTemplateRepository
    {
        $repo = new InMemoryFsNoteTemplateRepository();
        $repo->add(new FsNoteTemplate(
            id: '01HAAAAAAAAAAAAAAAAAAAAATT1',
            code: 'AP_INVENTORY',
            category: FsNoteCategory::AccountingPolicy,
            label: '棚卸資産の評価方法',
            defaultBody: '棚卸資産の評価は、主として総平均法による原価法によっております。',
            sortOrder: 10,
        ));
        $repo->add(new FsNoteTemplate(
            id: '01HAAAAAAAAAAAAAAAAAAAAATT2',
            code: 'AP_DEPRECIATION',
            category: FsNoteCategory::AccountingPolicy,
            label: '有形固定資産の減価償却方法',
            defaultBody: '定率法を採用しております。',
            sortOrder: 20,
        ));
        return $repo;
    }
}
