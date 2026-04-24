<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\StatementOfChangesInEquity;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\StatementOfChangesInEquity\CreateSsAdjustmentInput;
use Rucaro\Application\StatementOfChangesInEquity\CreateSsAdjustmentUseCase;
use Rucaro\Application\StatementOfChangesInEquity\DeleteSsAdjustmentUseCase;
use Rucaro\Application\StatementOfChangesInEquity\ListSsAdjustmentsUseCase;
use Rucaro\Application\StatementOfChangesInEquity\UpdateSsAdjustmentInput;
use Rucaro\Application\StatementOfChangesInEquity\UpdateSsAdjustmentUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemorySsManualAdjustmentRepository;

#[CoversClass(CreateSsAdjustmentUseCase::class)]
#[CoversClass(UpdateSsAdjustmentUseCase::class)]
#[CoversClass(DeleteSsAdjustmentUseCase::class)]
#[CoversClass(ListSsAdjustmentsUseCase::class)]
final class SsAdjustmentUseCasesTest extends TestCase
{
    private InMemorySsManualAdjustmentRepository $repo;
    private UlidGenerator $ulids;
    private CreateSsAdjustmentUseCase $create;
    private UpdateSsAdjustmentUseCase $update;
    private DeleteSsAdjustmentUseCase $delete;
    private ListSsAdjustmentsUseCase $list;

    protected function setUp(): void
    {
        $this->repo = new InMemorySsManualAdjustmentRepository();
        $this->ulids = new UlidGenerator(new FrozenClock());
        $this->create = new CreateSsAdjustmentUseCase($this->repo, $this->ulids);
        $this->update = new UpdateSsAdjustmentUseCase($this->repo);
        $this->delete = new DeleteSsAdjustmentUseCase($this->repo);
        $this->list   = new ListSsAdjustmentsUseCase($this->repo);
    }

    public function testCreatePersistsAndReturnsAdjustment(): void
    {
        $out = $this->create->execute($this->validInput());
        self::assertSame(SsSectionCode::RetainedEarnings, $out->adjustment->sectionCode);
        self::assertNotNull($this->repo->findById($out->adjustment->id));
    }

    public function testCreateRejectsNonUlidEntity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->create->execute(new CreateSsAdjustmentInput(
            entityId: 'not-a-ulid',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: SsSectionCode::CapitalStock,
            changeType: SsChangeType::NewIssue,
            amount: '1000.0000',
            label: 'x',
            sortOrder: 0,
            notes: null,
        ));
    }

    public function testUpdateOverwritesSelectedFields(): void
    {
        $created = $this->create->execute($this->validInput());
        $updated = $this->update->execute(new UpdateSsAdjustmentInput(
            id: $created->adjustment->id,
            amount: '-12345678.0000',
            label: 'Revised dividend',
        ));
        self::assertSame('-12345678.0000', $updated->adjustment->amount);
        self::assertSame('Revised dividend', $updated->adjustment->label);
        // Other fields preserved.
        self::assertSame(SsSectionCode::RetainedEarnings, $updated->adjustment->sectionCode);
    }

    public function testUpdateOfMissingRowRaisesValidation(): void
    {
        $this->expectException(ValidationException::class);
        $this->update->execute(new UpdateSsAdjustmentInput(
            id: '01HAAAAAAAAAAAAAAAAAAAAAZZ',
            amount: '1.0000',
        ));
    }

    public function testDeleteRemovesRow(): void
    {
        $created = $this->create->execute($this->validInput());
        $this->delete->execute($created->adjustment->id);
        self::assertNull($this->repo->findById($created->adjustment->id));
    }

    public function testDeleteOfMissingRowRaisesValidation(): void
    {
        $this->expectException(ValidationException::class);
        $this->delete->execute('01HAAAAAAAAAAAAAAAAAAAAAZZ');
    }

    public function testListReturnsRowsOrderedBySortOrder(): void
    {
        $this->create->execute($this->validInput(sortOrder: 5, label: 'later'));
        $this->create->execute($this->validInput(sortOrder: 1, label: 'earlier'));
        $rows = $this->list->execute('01HAAAAAAAAAAAAAAAAAAAAAA1', '01HAAAAAAAAAAAAAAAAAAAAAA2');
        self::assertCount(2, $rows);
        self::assertSame('earlier', $rows[0]->label);
        self::assertSame('later', $rows[1]->label);
    }

    private function validInput(int $sortOrder = 0, string $label = 'Dividend'): CreateSsAdjustmentInput
    {
        return new CreateSsAdjustmentInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: SsSectionCode::RetainedEarnings,
            changeType: SsChangeType::Dividend,
            amount: '-12000000.0000',
            label: $label,
            sortOrder: $sortOrder,
            notes: null,
        );
    }
}
