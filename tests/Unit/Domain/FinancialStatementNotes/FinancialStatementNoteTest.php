<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FinancialStatementNotes;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;

#[CoversClass(FinancialStatementNote::class)]
#[CoversClass(FsNoteCategory::class)]
final class FinancialStatementNoteTest extends TestCase
{
    public function testConstructorAcceptsValidData(): void
    {
        $n = self::make();
        self::assertSame('Plan note', $n->label);
        self::assertSame(FsNoteCategory::AccountingPolicy, $n->category);
    }

    public function testBlankLabelRejected(): void
    {
        $this->expectException(ValidationException::class);
        self::make(label: '');
    }

    public function testOversizedLabelRejected(): void
    {
        $this->expectException(ValidationException::class);
        self::make(label: str_repeat('あ', 129));
    }

    public function testBlankBodyRejected(): void
    {
        $this->expectException(ValidationException::class);
        self::make(body: '');
    }

    public function testNegativeSortOrderRejected(): void
    {
        $this->expectException(ValidationException::class);
        self::make(sortOrder: -1);
    }

    public function testTemplateCodeTooLongRejected(): void
    {
        $this->expectException(ValidationException::class);
        self::make(templateCode: str_repeat('x', 33));
    }

    public function testWithContentReplacesFields(): void
    {
        $n = self::make();
        $later = new DateTimeImmutable('2026-06-01T00:00:00Z');
        $updated = $n->withContent(FsNoteCategory::PlNotes, 'New', 'Body 2', $later);

        self::assertSame(FsNoteCategory::PlNotes, $updated->category);
        self::assertSame('New', $updated->label);
        self::assertSame('Body 2', $updated->body);
        self::assertSame($later, $updated->updatedAt);
        // Original is unchanged (immutable).
        self::assertSame('Plan note', $n->label);
    }

    public function testWithActiveFlipsFlag(): void
    {
        $n = self::make();
        $updated = $n->withActive(false, new DateTimeImmutable('2026-06-01T00:00:00Z'));
        self::assertFalse($updated->isActive);
        self::assertTrue($n->isActive);
    }

    public function testWithSortOrderUpdates(): void
    {
        $n = self::make();
        $u = $n->withSortOrder(42, new DateTimeImmutable('2026-06-01T00:00:00Z'));
        self::assertSame(42, $u->sortOrder);
        self::assertSame(0, $n->sortOrder);
    }

    public function testCategoryLabelAndOrder(): void
    {
        self::assertSame('重要な会計方針', FsNoteCategory::AccountingPolicy->jaLabel());
        self::assertSame(10, FsNoteCategory::AccountingPolicy->displayOrder());
        self::assertSame(90, FsNoteCategory::Other->displayOrder());
    }

    private static function make(
        string $label = 'Plan note',
        string $body = 'Body text',
        int $sortOrder = 0,
        ?string $templateCode = null,
    ): FinancialStatementNote {
        $now = new DateTimeImmutable('2026-04-21T12:00:00Z');
        return new FinancialStatementNote(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA3',
            templateCode: $templateCode,
            category: FsNoteCategory::AccountingPolicy,
            label: $label,
            body: $body,
            sortOrder: $sortOrder,
            isActive: true,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
