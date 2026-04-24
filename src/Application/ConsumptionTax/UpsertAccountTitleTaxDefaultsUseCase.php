<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use DateTimeImmutable;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefaultRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Bulk-set the tax defaults for an entity's chart of accounts.
 *
 * Accepts a payload `[{ accountTitleId, categoryCode, rateCode? }, ...]`
 * and upserts them transactionally.
 */
final readonly class UpsertAccountTitleTaxDefaultsUseCase
{
    public function __construct(
        private AccountTitleConsumptionTaxDefaultRepositoryInterface $defaults,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @param list<array{accountTitleId: string, categoryCode: string, rateCode?: ?string}> $rows
     * @return list<AccountTitleConsumptionTaxDefault>
     */
    public function execute(string $entityId, array $rows): array
    {
        $now = $this->clock->getCurrentTime();
        /** @var list<AccountTitleConsumptionTaxDefault> $models */
        $models = [];
        foreach ($rows as $i => $row) {
            if (!isset($row['accountTitleId']) || !is_string($row['accountTitleId'])) {
                throw ValidationException::withErrors([
                    sprintf('rows[%d].accountTitleId', $i) => ['accountTitleId is required.'],
                ]);
            }
            if (!isset($row['categoryCode']) || !is_string($row['categoryCode'])) {
                throw ValidationException::withErrors([
                    sprintf('rows[%d].categoryCode', $i) => ['categoryCode is required.'],
                ]);
            }
            $category = ConsumptionTaxCategoryCode::tryFrom($row['categoryCode']);
            if ($category === null) {
                throw ValidationException::withErrors([
                    sprintf('rows[%d].categoryCode', $i) => ['categoryCode must be a known code.'],
                ]);
            }
            $rateCode = null;
            if (array_key_exists('rateCode', $row) && $row['rateCode'] !== null) {
                $rateCode = (string) $row['rateCode'];
            }
            $existing = $this->defaults->findByAccountTitle($entityId, $row['accountTitleId']);
            $id = $existing?->id ?? $this->ulids->generate();
            $createdAt = $existing?->createdAt ?? $now;
            $models[] = new AccountTitleConsumptionTaxDefault(
                id: $id,
                entityId: $entityId,
                accountTitleId: $row['accountTitleId'],
                defaultCategoryCode: $category,
                defaultRateCode: $rateCode,
                createdAt: self::asUtc($createdAt),
                updatedAt: $now,
            );
        }
        $this->defaults->saveAll($models);
        return $models;
    }

    private static function asUtc(DateTimeImmutable $d): DateTimeImmutable
    {
        return $d;
    }
}
