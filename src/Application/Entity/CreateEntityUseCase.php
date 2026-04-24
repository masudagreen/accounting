<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Entity\EntityRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

final readonly class CreateEntityUseCase
{
    public function __construct(
        private EntityRepositoryInterface $repo,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateEntityUseCaseInput $input): Entity
    {
        $errors = EntityValidator::validate(
            $input->name,
            $input->nationCode,
            $input->currencyCode,
            $input->fiscalStartMmDd,
        );
        if ($errors !== []) {
            throw ValidationException::withErrors($errors);
        }
        $now = $this->clock->getCurrentTime();
        $entity = new Entity(
            id: $this->ulids->generate(),
            ownerUserId: $input->ownerUserId,
            name: $input->name,
            nationCode: $input->nationCode,
            currencyCode: $input->currencyCode,
            fiscalStartMmDd: $input->fiscalStartMmDd,
            isActive: $input->isActive,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
            isCorporate: $input->isCorporate,
        );
        $this->repo->save($entity);
        return $entity;
    }
}
