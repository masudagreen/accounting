<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use InvalidArgumentException;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;
use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Domain\Entity\EntityRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Create a fresh {@see BlueReturnForm} in {@see BlueReturnStatus::Draft}.
 *
 * Guards:
 *   - entityId + fiscalTermId must be valid ULIDs;
 *   - the referenced entity must exist AND be flagged as an individual
 *     entrepreneur (`isCorporate=false`); corporate entities raise a
 *     {@see ValidationException} so the HTTP layer can return 422.
 *   - one form per (entity, fiscal term) — duplicate creation is a 422.
 */
final readonly class CreateBlueReturnUseCase
{
    public function __construct(
        private BlueReturnRepositoryInterface $forms,
        private EntityRepositoryInterface $entities,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateBlueReturnInput $input): BlueReturnOutput
    {
        if (!UlidGenerator::isValid($input->entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->fiscalTermId)) {
            throw new InvalidArgumentException('fiscalTermId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->createdBy)) {
            throw new InvalidArgumentException('createdBy must be a ULID.');
        }

        $entity = $this->entities->findById($input->entityId);
        if ($entity === null) {
            throw ValidationException::withErrors([
                'entityId' => [sprintf('entity %s was not found.', $input->entityId)],
            ]);
        }
        if ($entity->isCorporate) {
            throw ValidationException::withErrors([
                'entityId' => ['blue return forms are only available for individual entrepreneurs.'],
            ]);
        }

        $existing = $this->forms->findByEntityAndFiscalTerm($input->entityId, $input->fiscalTermId);
        if ($existing !== null) {
            throw ValidationException::withErrors([
                'fiscalTermId' => ['a blue return form already exists for this fiscal term.'],
            ]);
        }

        $snapshot = $input->snapshot === []
            ? BlueReturnSnapshot::empty($input->formType)
            : BlueReturnSnapshot::fromArray($input->snapshot);

        $now = $this->clock->getCurrentTime();
        $form = new BlueReturnForm(
            id: $this->ulids->generate(),
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            formType: $input->formType,
            status: BlueReturnStatus::Draft,
            snapshot: $snapshot,
            finalizedAt: null,
            createdBy: $input->createdBy,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
        );
        $this->forms->save($form);
        return new BlueReturnOutput($form);
    }
}
