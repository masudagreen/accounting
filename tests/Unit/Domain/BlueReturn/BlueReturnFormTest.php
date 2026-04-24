<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\BlueReturn;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Domain\Exception\InvariantViolationException;

#[CoversClass(BlueReturnForm::class)]
#[CoversClass(BlueReturnStatus::class)]
#[CoversClass(BlueReturnFormType::class)]
final class BlueReturnFormTest extends TestCase
{
    public function testFinalizeMovesDraftToFinalized(): void
    {
        $form = $this->draftForm();
        $now = new DateTimeImmutable('2026-03-15T09:00:00Z');

        $finalized = $form->finalize($now);

        self::assertSame(BlueReturnStatus::Finalized, $finalized->status);
        self::assertEquals($now, $finalized->finalizedAt);
        // Source form is immutable — original still Draft.
        self::assertSame(BlueReturnStatus::Draft, $form->status);
    }

    public function testFinalizeRejectsAlreadyFinalized(): void
    {
        $form = $this->draftForm()->finalize(new DateTimeImmutable('2026-03-15T09:00:00Z'));
        $this->expectException(InvariantViolationException::class);
        $form->finalize(new DateTimeImmutable('2026-03-16T09:00:00Z'));
    }

    public function testWithSnapshotRejectedOnceFinalized(): void
    {
        $form = $this->draftForm()->finalize(new DateTimeImmutable('2026-03-15T09:00:00Z'));
        $this->expectException(InvariantViolationException::class);
        $form->withSnapshot(
            BlueReturnSnapshot::empty(BlueReturnFormType::General),
            new DateTimeImmutable('2026-03-16T09:00:00Z'),
        );
    }

    public function testWithSnapshotSucceedsWhileDraft(): void
    {
        $form = $this->draftForm();
        $newSnapshot = new BlueReturnSnapshot(
            page1Pl: ['formType' => 'general', 'netIncome' => '123'],
            page2Monthly: [],
            page3Breakdown: [],
            page4Bs: [],
        );

        $updated = $form->withSnapshot($newSnapshot, new DateTimeImmutable('2026-03-16T09:00:00Z'));

        self::assertSame('123', $updated->snapshot->page1Pl['netIncome']);
        self::assertSame(BlueReturnStatus::Draft, $updated->status);
    }

    public function testWithFormTypeRejectedOnceFinalized(): void
    {
        $form = $this->draftForm()->finalize(new DateTimeImmutable('2026-03-15T09:00:00Z'));
        $this->expectException(InvariantViolationException::class);
        $form->withFormType(BlueReturnFormType::Agricultural, new DateTimeImmutable('2026-03-16T09:00:00Z'));
    }

    public function testSnapshotFromArrayFillsMissingKeys(): void
    {
        $snap = BlueReturnSnapshot::fromArray(['page1_pl' => ['netIncome' => '42']]);
        self::assertSame('42', $snap->page1Pl['netIncome']);
        self::assertSame([], $snap->page2Monthly);
        self::assertSame([], $snap->page3Breakdown);
        self::assertSame([], $snap->page4Bs);
    }

    public function testSnapshotToArrayRoundTrips(): void
    {
        $original = new BlueReturnSnapshot(
            page1Pl: ['a' => 1],
            page2Monthly: ['b' => 2],
            page3Breakdown: ['c' => 3],
            page4Bs: ['d' => 4],
        );
        $roundTripped = BlueReturnSnapshot::fromArray($original->toArray());
        self::assertSame($original->toArray(), $roundTripped->toArray());
    }

    public function testEmptyHasAllFourPages(): void
    {
        $snap = BlueReturnSnapshot::empty(BlueReturnFormType::RealEstate);
        self::assertSame('real_estate', $snap->page1Pl['formType']);
        self::assertArrayHasKey('months', $snap->page2Monthly);
        self::assertArrayHasKey('depreciation', $snap->page3Breakdown);
        self::assertArrayHasKey('assets', $snap->page4Bs);
    }

    private function draftForm(): BlueReturnForm
    {
        $now = new DateTimeImmutable('2026-01-10T09:00:00Z');
        return new BlueReturnForm(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            formType: BlueReturnFormType::General,
            status: BlueReturnStatus::Draft,
            snapshot: BlueReturnSnapshot::empty(BlueReturnFormType::General),
            finalizedAt: null,
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAB3',
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
