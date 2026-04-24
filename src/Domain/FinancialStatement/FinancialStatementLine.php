<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement;

use Rucaro\Support\Decimal\Decimal;

/**
 * One row in a {@see Section} — typically an account title with an amount, but
 * also used for computed subtotals (`isSubtotal = true`, `accountTitleId = null`).
 *
 * The field set matches the OpenAPI `FinancialStatementLine` schema in
 * `docs/api/openapi.yaml`.
 */
final readonly class FinancialStatementLine
{
    public function __construct(
        public string $label,
        public string $amount,
        public ?string $accountTitleId = null,
        public ?string $accountTitleCode = null,
        public int $depth = 0,
        public bool $isSubtotal = false,
    ) {
    }

    public static function ofAccount(
        string $accountTitleId,
        string $accountTitleCode,
        string $label,
        string $amount,
        int $depth = 1,
    ): self {
        return new self(
            label: $label,
            amount: Decimal::normalize($amount),
            accountTitleId: $accountTitleId,
            accountTitleCode: $accountTitleCode,
            depth: $depth,
            isSubtotal: false,
        );
    }

    public static function subtotal(string $label, string $amount, int $depth = 0): self
    {
        return new self(
            label: $label,
            amount: Decimal::normalize($amount),
            accountTitleId: null,
            accountTitleCode: null,
            depth: $depth,
            isSubtotal: true,
        );
    }
}
