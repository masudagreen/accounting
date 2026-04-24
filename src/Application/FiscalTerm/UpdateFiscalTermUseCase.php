<?php

declare(strict_types=1);

namespace Rucaro\Application\FiscalTerm;

use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FiscalTerm\FiscalTerm;
use Rucaro\Domain\FiscalTerm\FiscalTermRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

final readonly class UpdateFiscalTermUseCase
{
    public function __construct(
        private FiscalTermRepositoryInterface $repo,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateFiscalTermUseCaseInput $input): FiscalTerm
    {
        $existing = $this->repo->findById($input->id);
        if ($existing === null) {
            throw EntityNotFoundException::for('FiscalTerm', $input->id);
        }
        $result = FiscalTermValidator::validate($input->fiscalPeriod, $input->startDate, $input->endDate);
        $errors = $result['errors'];
        if ($errors === [] && $this->repo->existsByPeriod($existing->entityId, $input->fiscalPeriod, $input->id)) {
            $errors['fiscal_period'][] = 'この期番号は既に使用されています。';
        }
        if ($errors !== [] || $result['startDate'] === null || $result['endDate'] === null) {
            throw ValidationException::withErrors($errors);
        }
        $now = $this->clock->getCurrentTime();
        $closedAt = $existing->closedAt;
        if ($input->isClosed && !$existing->isClosed) {
            $closedAt = $now;
        } elseif (!$input->isClosed) {
            $closedAt = null;
        }
        $updated = new FiscalTerm(
            id: $existing->id,
            entityId: $existing->entityId,
            fiscalPeriod: $input->fiscalPeriod,
            startDate: $result['startDate'],
            endDate: $result['endDate'],
            isClosed: $input->isClosed,
            closedAt: $closedAt,
            createdAt: $existing->createdAt,
            updatedAt: $now,
        );
        $this->repo->save($updated);
        return $updated;
    }
}
