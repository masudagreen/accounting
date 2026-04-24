<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * Input to a consumption-tax calculator.
 *
 * Represents one classified line (produced from `journal_entry_lines` by
 * the aggregator, or from external data in tests). `amountExcludingTax`
 * is always the 本体価格 (scale-4); `taxAmount` is already computed at
 * journaling time — we don't re-derive it here.
 *
 * Invariants:
 *   - amountExcludingTax and taxAmount are non-negative scale-4 decimals;
 *   - when categoryCode is non-taxable/untaxed, taxAmount must be 0.
 */
final readonly class TaxableTransaction
{
    public function __construct(
        public DateTimeImmutable $bookedOn,
        public ConsumptionTaxCategoryCode $categoryCode,
        public string $ratePercent,
        public bool $isReduced,
        public string $amountExcludingTax,
        public string $taxAmount,
        public ?string $counterpartyRegistrationNumber = null,
    ) {
        if (Decimal::compare($amountExcludingTax, '0.0000') < 0) {
            throw ValidationException::withErrors([
                'amountExcludingTax' => ['amountExcludingTax must be >= 0.'],
            ]);
        }
        if (Decimal::compare($taxAmount, '0.0000') < 0) {
            throw ValidationException::withErrors([
                'taxAmount' => ['taxAmount must be >= 0.'],
            ]);
        }
        if (!$categoryCode->isTaxable() && Decimal::compare($taxAmount, '0.0000') !== 0) {
            throw ValidationException::withErrors([
                'taxAmount' => ['taxAmount must be 0 for non-taxable categories.'],
            ]);
        }
    }
}
