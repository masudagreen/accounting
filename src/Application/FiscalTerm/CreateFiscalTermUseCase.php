<?php

declare(strict_types=1);

namespace Rucaro\Application\FiscalTerm;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FiscalTerm\FiscalTerm;
use Rucaro\Domain\FiscalTerm\FiscalTermRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

final readonly class CreateFiscalTermUseCase
{
    public function __construct(
        private FiscalTermRepositoryInterface $repo,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateFiscalTermUseCaseInput $input): FiscalTerm
    {
        $result = FiscalTermValidator::validate($input->fiscalPeriod, $input->startDate, $input->endDate);
        $errors = $result['errors'];
        if ($errors === [] && $this->repo->existsByPeriod($input->entityId, $input->fiscalPeriod)) {
            $errors['fiscal_period'][] = 'この期番号は既に使用されています。';
        }
        if ($errors !== [] || $result['startDate'] === null || $result['endDate'] === null) {
            throw ValidationException::withErrors($errors);
        }
        $now = $this->clock->getCurrentTime();
        $term = new FiscalTerm(
            id: $this->ulids->generate(),
            entityId: $input->entityId,
            fiscalPeriod: $input->fiscalPeriod,
            startDate: $result['startDate'],
            endDate: $result['endDate'],
            isClosed: $input->isClosed,
            closedAt: $input->isClosed ? $now : null,
            createdAt: $now,
            updatedAt: $now,
        );
        $this->repo->save($term);
        return $term;
    }
}
