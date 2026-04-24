<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use DateTimeImmutable;

/**
 * Mapping between an account title and its default consumption-tax
 * category / rate for a given entity.
 *
 * The legacy system carried these defaults inside
 * `accountingAccountTitleJpn.jsonConsumptionTax*`. We move them into a
 * proper mapping table so the UI can do bulk edits without rewriting
 * JSON blobs.
 */
final readonly class AccountTitleConsumptionTaxDefault
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $accountTitleId,
        public ConsumptionTaxCategoryCode $defaultCategoryCode,
        public ?string $defaultRateCode,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
