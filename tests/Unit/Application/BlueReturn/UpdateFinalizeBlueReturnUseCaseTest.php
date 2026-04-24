<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\BlueReturn;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\BlueReturn\CreateBlueReturnInput;
use Rucaro\Application\BlueReturn\CreateBlueReturnUseCase;
use Rucaro\Application\BlueReturn\DeleteBlueReturnUseCase;
use Rucaro\Application\BlueReturn\FinalizeBlueReturnUseCase;
use Rucaro\Application\BlueReturn\UpdateBlueReturnInput;
use Rucaro\Application\BlueReturn\UpdateBlueReturnUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBlueReturnRepository;
use Rucaro\Tests\Unit\Application\Support\InMemoryEntityRepo;

#[CoversClass(UpdateBlueReturnUseCase::class)]
#[CoversClass(FinalizeBlueReturnUseCase::class)]
#[CoversClass(DeleteBlueReturnUseCase::class)]
final class UpdateFinalizeBlueReturnUseCaseTest extends TestCase
{
    private const ENTITY_ID = '01HAAAAAAAAAAAAAAAAAAAAAB1';
    private const FISCAL_TERM_ID = '01HAAAAAAAAAAAAAAAAAAAAAB2';
    private const OWNER_ID = '01HAAAAAAAAAAAAAAAAAAAAAB3';

    public function testUpdateMutatesSnapshotForDraft(): void
    {
        $ctx = $this->setUpWithDraft();
        $updateUc = new UpdateBlueReturnUseCase($ctx['repo'], new FrozenClock());

        $out = $updateUc->execute(new UpdateBlueReturnInput(
            id: $ctx['id'],
            formType: null,
            snapshot: ['page1_pl' => ['netIncome' => '777']],
        ));

        self::assertSame('777', $out->form->snapshot->page1Pl['netIncome']);
    }

    public function testUpdateCanChangeFormType(): void
    {
        $ctx = $this->setUpWithDraft();
        $updateUc = new UpdateBlueReturnUseCase($ctx['repo'], new FrozenClock());
        $out = $updateUc->execute(new UpdateBlueReturnInput(
            id: $ctx['id'],
            formType: BlueReturnFormType::Agricultural,
            snapshot: null,
        ));
        self::assertSame(BlueReturnFormType::Agricultural, $out->form->formType);
    }

    public function testFinalizePromotesDraftToFinalized(): void
    {
        $ctx = $this->setUpWithDraft();
        $finalizeUc = new FinalizeBlueReturnUseCase($ctx['repo'], new FrozenClock());
        $out = $finalizeUc->execute($ctx['id']);
        self::assertSame(BlueReturnStatus::Finalized, $out->form->status);
    }

    public function testUpdateRejectedAfterFinalize(): void
    {
        $ctx = $this->setUpWithDraft();
        $finalizeUc = new FinalizeBlueReturnUseCase($ctx['repo'], new FrozenClock());
        $finalizeUc->execute($ctx['id']);

        $updateUc = new UpdateBlueReturnUseCase($ctx['repo'], new FrozenClock());
        $this->expectException(InvariantViolationException::class);
        $updateUc->execute(new UpdateBlueReturnInput(
            id: $ctx['id'],
            formType: null,
            snapshot: ['page1_pl' => ['netIncome' => '999']],
        ));
    }

    public function testDeleteBlockedForFinalized(): void
    {
        $ctx = $this->setUpWithDraft();
        $finalizeUc = new FinalizeBlueReturnUseCase($ctx['repo'], new FrozenClock());
        $finalizeUc->execute($ctx['id']);

        $deleteUc = new DeleteBlueReturnUseCase($ctx['repo']);
        $this->expectException(InvariantViolationException::class);
        $deleteUc->execute($ctx['id']);
    }

    public function testDeleteDraftIsIdempotent(): void
    {
        $ctx = $this->setUpWithDraft();
        $deleteUc = new DeleteBlueReturnUseCase($ctx['repo']);
        $deleteUc->execute($ctx['id']);
        // second delete is a no-op
        $deleteUc->execute($ctx['id']);
        self::assertNull($ctx['repo']->findById($ctx['id']));
    }

    /**
     * @return array{repo: InMemoryBlueReturnRepository, id: string}
     */
    private function setUpWithDraft(): array
    {
        $repo = new InMemoryBlueReturnRepository();
        $entities = new InMemoryEntityRepo();
        $entities->add($this->individualEntity());
        $createUc = new CreateBlueReturnUseCase(
            $repo,
            $entities,
            new UlidGenerator(new FrozenClock()),
            new FrozenClock(),
        );
        $out = $createUc->execute(new CreateBlueReturnInput(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_TERM_ID,
            formType: BlueReturnFormType::General,
            snapshot: [],
            createdBy: self::OWNER_ID,
        ));
        return ['repo' => $repo, 'id' => $out->form->id];
    }

    private function individualEntity(): Entity
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new Entity(
            id: self::ENTITY_ID,
            ownerUserId: self::OWNER_ID,
            name: 'Sole Proprietor',
            nationCode: 'JPN',
            currencyCode: 'JPY',
            fiscalStartMmDd: '0101',
            isActive: true,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
            isCorporate: false,
        );
    }
}
