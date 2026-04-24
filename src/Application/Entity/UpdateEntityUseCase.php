<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Entity\EntityRepositoryInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Clock\ClockInterface;

final readonly class UpdateEntityUseCase
{
    public function __construct(
        private EntityRepositoryInterface $repo,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateEntityUseCaseInput $input): Entity
    {
        $existing = $this->repo->findById($input->id);
        if ($existing === null) {
            throw EntityNotFoundException::for('Entity', $input->id);
        }
        $errors = EntityValidator::validate(
            $input->name,
            $input->nationCode,
            $input->currencyCode,
            $input->fiscalStartMmDd,
        );
        if ($errors !== []) {
            throw ValidationException::withErrors($errors);
        }
        $updated = new Entity(
            id: $existing->id,
            ownerUserId: $existing->ownerUserId,
            name: $input->name,
            nationCode: $input->nationCode,
            currencyCode: $input->currencyCode,
            fiscalStartMmDd: $input->fiscalStartMmDd,
            isActive: $input->isActive,
            createdAt: $existing->createdAt,
            updatedAt: $this->clock->getCurrentTime(),
            deletedAt: $existing->deletedAt,
            isCorporate: $input->isCorporate,
        );
        $this->repo->save($updated);
        return $updated;
    }
}
