<?php

declare(strict_types=1);

namespace Rucaro\Application\BreakEvenPoint;

use InvalidArgumentException;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassificationRepositoryInterface;
use Rucaro\Domain\BreakEvenPoint\CvpCostType;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Bulk upsert of CVP classifications for one entity.
 *
 * Each input row is normalised through
 * {@see AccountTitleCvpClassification::canonicalise()} so the stored
 * `variable_ratio` matches the declared `cost_type` (except for
 * `semi_variable`, where callers supply an explicit 0..1 ratio).
 */
final readonly class UpsertCvpClassificationsUseCase
{
    public function __construct(
        private AccountTitleCvpClassificationRepositoryInterface $repo,
    ) {
    }

    /**
     * @param list<UpsertCvpClassificationInput> $rows
     * @return list<AccountTitleCvpClassification>
     */
    public function execute(string $entityId, array $rows): array
    {
        if (!UlidGenerator::isValid($entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        $built = [];
        foreach ($rows as $idx => $row) {
            if (!UlidGenerator::isValid($row->accountTitleId)) {
                throw ValidationException::withErrors([
                    "rows.$idx.accountTitleId" => ['accountTitleId must be a ULID.'],
                ]);
            }
            try {
                $type = CvpCostType::fromString($row->costType);
            } catch (InvalidArgumentException $e) {
                throw ValidationException::withErrors([
                    "rows.$idx.costType" => [$e->getMessage()],
                ]);
            }
            $built[] = AccountTitleCvpClassification::canonicalise(
                entityId: $entityId,
                accountTitleId: $row->accountTitleId,
                type: $type,
                variableRatio: $row->variableRatio,
                notes: $row->notes,
            );
        }
        $this->repo->saveMany($built);
        return $built;
    }
}
