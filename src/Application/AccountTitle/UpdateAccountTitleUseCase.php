<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Update an existing {@see AccountTitle}.
 */
final readonly class UpdateAccountTitleUseCase
{
    public function __construct(
        private AccountTitleRepositoryInterface $repo,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateAccountTitleUseCaseInput $input): AccountTitle
    {
        $existing = $this->repo->findById($input->id);
        if ($existing === null) {
            throw EntityNotFoundException::for('AccountTitle', $input->id);
        }

        $errors = AccountTitleValidator::validate($input->code, $input->name, $input->category, $input->normalSide);
        if ($input->parentId !== null && $input->parentId !== '') {
            if (!UlidGenerator::isValid($input->parentId)) {
                $errors['parent_id'][] = '親勘定科目 ID の形式が不正です。';
            } elseif ($input->parentId === $input->id) {
                $errors['parent_id'][] = '自分自身を親に設定することはできません。';
            }
        }
        if ($errors === [] && $this->repo->existsByCode($existing->entityId, $input->code, $input->id)) {
            $errors['code'][] = 'このコードは既に使用されています。';
        }
        if ($errors !== []) {
            throw ValidationException::withErrors($errors);
        }

        $updated = new AccountTitle(
            id: $existing->id,
            entityId: $existing->entityId,
            code: $input->code,
            name: $input->name,
            category: $input->category,
            normalSide: $input->normalSide,
            parentId: $input->parentId === '' ? null : $input->parentId,
            sortOrder: $input->sortOrder,
            isActive: $input->isActive,
            createdAt: $existing->createdAt,
            updatedAt: $this->clock->getCurrentTime(),
        );
        $this->repo->save($updated);
        return $updated;
    }
}
