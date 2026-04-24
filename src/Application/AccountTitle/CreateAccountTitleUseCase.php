<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Create a single {@see AccountTitle}.
 *
 * Validation is intentionally keyed by the field name that the UI form uses
 * so the Web controller can render error messages back onto the right input
 * without re-mapping names.
 */
final readonly class CreateAccountTitleUseCase
{
    public function __construct(
        private AccountTitleRepositoryInterface $repo,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateAccountTitleUseCaseInput $input): AccountTitle
    {
        $errors = AccountTitleValidator::validate($input->code, $input->name, $input->category, $input->normalSide);
        if ($input->parentId !== null && $input->parentId !== '' && !UlidGenerator::isValid($input->parentId)) {
            $errors['parent_id'][] = '親勘定科目 ID の形式が不正です。';
        }
        if ($errors === [] && $this->repo->existsByCode($input->entityId, $input->code)) {
            $errors['code'][] = 'このコードは既に使用されています。';
        }
        if ($errors !== []) {
            throw ValidationException::withErrors($errors);
        }

        $now = $this->clock->getCurrentTime();
        $title = new AccountTitle(
            id: $this->ulids->generate(),
            entityId: $input->entityId,
            code: $input->code,
            name: $input->name,
            category: $input->category,
            normalSide: $input->normalSide,
            parentId: $input->parentId === '' ? null : $input->parentId,
            sortOrder: $input->sortOrder,
            isActive: $input->isActive,
            createdAt: $now,
            updatedAt: $now,
        );
        $this->repo->save($title);
        return $title;
    }
}
