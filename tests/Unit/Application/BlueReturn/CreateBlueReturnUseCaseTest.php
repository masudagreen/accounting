<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\BlueReturn;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\BlueReturn\CreateBlueReturnInput;
use Rucaro\Application\BlueReturn\CreateBlueReturnUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBlueReturnRepository;
use Rucaro\Tests\Unit\Application\Support\InMemoryEntityRepo;

#[CoversClass(CreateBlueReturnUseCase::class)]
final class CreateBlueReturnUseCaseTest extends TestCase
{
    private const ENTITY_ID = '01HAAAAAAAAAAAAAAAAAAAAAB1';
    private const FISCAL_TERM_ID = '01HAAAAAAAAAAAAAAAAAAAAAB2';
    private const OWNER_ID = '01HAAAAAAAAAAAAAAAAAAAAAB3';

    public function testCreatesDraftFormForIndividualEntrepreneur(): void
    {
        $repo = new InMemoryBlueReturnRepository();
        $entities = new InMemoryEntityRepo();
        $entities->add($this->individualEntity());
        $uc = new CreateBlueReturnUseCase($repo, $entities, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $out = $uc->execute(new CreateBlueReturnInput(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_TERM_ID,
            formType: BlueReturnFormType::General,
            snapshot: [],
            createdBy: self::OWNER_ID,
        ));

        self::assertSame(BlueReturnStatus::Draft, $out->form->status);
        self::assertSame(BlueReturnFormType::General, $out->form->formType);
        self::assertNotNull($repo->findById($out->form->id));
    }

    public function testRejectsCorporateEntity(): void
    {
        $repo = new InMemoryBlueReturnRepository();
        $entities = new InMemoryEntityRepo();
        $entities->add($this->corporateEntity());
        $uc = new CreateBlueReturnUseCase($repo, $entities, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $this->expectException(ValidationException::class);
        $uc->execute(new CreateBlueReturnInput(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_TERM_ID,
            formType: BlueReturnFormType::General,
            snapshot: [],
            createdBy: self::OWNER_ID,
        ));
    }

    public function testRejectsDuplicateFormForSameFiscalTerm(): void
    {
        $repo = new InMemoryBlueReturnRepository();
        $entities = new InMemoryEntityRepo();
        $entities->add($this->individualEntity());
        $uc = new CreateBlueReturnUseCase($repo, $entities, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $input = new CreateBlueReturnInput(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_TERM_ID,
            formType: BlueReturnFormType::General,
            snapshot: [],
            createdBy: self::OWNER_ID,
        );
        $uc->execute($input);
        $this->expectException(ValidationException::class);
        $uc->execute($input);
    }

    public function testRejectsMissingEntity(): void
    {
        $repo = new InMemoryBlueReturnRepository();
        $entities = new InMemoryEntityRepo();
        $uc = new CreateBlueReturnUseCase($repo, $entities, new UlidGenerator(new FrozenClock()), new FrozenClock());
        $this->expectException(ValidationException::class);
        $uc->execute(new CreateBlueReturnInput(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_TERM_ID,
            formType: BlueReturnFormType::General,
            snapshot: [],
            createdBy: self::OWNER_ID,
        ));
    }

    public function testHydratesProvidedSnapshot(): void
    {
        $repo = new InMemoryBlueReturnRepository();
        $entities = new InMemoryEntityRepo();
        $entities->add($this->individualEntity());
        $uc = new CreateBlueReturnUseCase($repo, $entities, new UlidGenerator(new FrozenClock()), new FrozenClock());

        $out = $uc->execute(new CreateBlueReturnInput(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_TERM_ID,
            formType: BlueReturnFormType::RealEstate,
            snapshot: ['page1_pl' => ['formType' => 'real_estate', 'netIncome' => '500000']],
            createdBy: self::OWNER_ID,
        ));

        self::assertSame('500000', $out->form->snapshot->page1Pl['netIncome']);
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

    private function corporateEntity(): Entity
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new Entity(
            id: self::ENTITY_ID,
            ownerUserId: self::OWNER_ID,
            name: 'Kabushiki Kaisha',
            nationCode: 'JPN',
            currencyCode: 'JPY',
            fiscalStartMmDd: '0401',
            isActive: true,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
            isCorporate: true,
        );
    }
}
