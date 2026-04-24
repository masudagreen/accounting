<?php

declare(strict_types=1);

namespace Rucaro\Application\SubAccountTitle;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\SubAccountTitle\SubAccountTitle;
use Rucaro\Domain\SubAccountTitle\SubAccountTitleRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

final readonly class CreateSubAccountTitleUseCase
{
    public function __construct(
        private SubAccountTitleRepositoryInterface $repo,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateSubAccountTitleUseCaseInput $input): SubAccountTitle
    {
        $errors = SubAccountTitleValidator::validate($input->accountTitleId, $input->code, $input->name);
        if ($errors === [] && !UlidGenerator::isValid($input->accountTitleId)) {
            $errors['account_title_id'][] = '勘定科目 ID の形式が不正です。';
        }
        if ($errors === [] && $this->repo->existsByCode($input->accountTitleId, $input->code)) {
            $errors['code'][] = 'このコードは既に使用されています。';
        }
        if ($errors !== []) {
            throw ValidationException::withErrors($errors);
        }

        $now = $this->clock->getCurrentTime();
        $sub = new SubAccountTitle(
            id: $this->ulids->generate(),
            accountTitleId: $input->accountTitleId,
            code: $input->code,
            name: $input->name,
            sortOrder: $input->sortOrder,
            isActive: $input->isActive,
            createdAt: $now,
            updatedAt: $now,
        );
        $this->repo->save($sub);
        return $sub;
    }
}
