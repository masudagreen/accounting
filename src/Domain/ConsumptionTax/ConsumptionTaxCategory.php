<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use Rucaro\Domain\Exception\ValidationException;

/**
 * Readonly row from `consumption_tax_categories`.
 */
final readonly class ConsumptionTaxCategory
{
    public function __construct(
        public string $id,
        public ConsumptionTaxCategoryCode $code,
        public string $label,
        public string $side,
        public bool $deductible,
        public int $sortOrder = 0,
    ) {
        if (!in_array($side, ['sales', 'purchase', 'both'], true)) {
            throw ValidationException::withErrors([
                'side' => ['side must be sales / purchase / both.'],
            ]);
        }
    }
}
