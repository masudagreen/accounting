<?php

declare(strict_types=1);

namespace Rucaro\Application\SubAccountTitle;

use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\SubAccountTitle\SubAccountTitle;
use Rucaro\Domain\SubAccountTitle\SubAccountTitleRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

final readonly class UpdateSubAccountTitleUseCase
{
    public function __construct(
        private SubAccountTitleRepositoryInterface $repo,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateSubAccountTitleUseCaseInput $input): SubAccountTitle
    {
        $existing = $this->repo->findById($input->id);
        if ($existing === null) {
            throw EntityNotFoundException::for('SubAccountTitle', $input->id);
        }
        $errors = SubAccountTitleValidator::validate($existing->accountTitleId, $input->code, $input->name);
        if ($errors === [] && $this->repo->existsByCode($existing->accountTitleId, $input->code, $input->id)) {
            $errors['code'][] = 'このコードは既に使用されています。';
        }
        if ($errors !== []) {
            throw ValidationException::withErrors($errors);
        }

        $updated = new SubAccountTitle(
            id: $existing->id,
            accountTitleId: $existing->accountTitleId,
            code: $input->code,
            name: $input->name,
            sortOrder: $input->sortOrder,
            isActive: $input->isActive,
            createdAt: $existing->createdAt,
            updatedAt: $this->clock->getCurrentTime(),
        );
        $this->repo->save($updated);
        return $updated;
    }
}
