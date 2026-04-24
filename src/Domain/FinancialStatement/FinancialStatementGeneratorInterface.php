<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement;

/**
 * Port for producing a binary representation of a {@see FinancialStatement}.
 *
 * The default implementation (Infrastructure layer) renders Smarty HTML and
 * runs it through dompdf. Additional adapters (e.g. Excel export) would
 * implement this interface without touching the Domain layer.
 */
interface FinancialStatementGeneratorInterface
{
    /**
     * Render the statement as PDF and return the raw bytes.
     */
    public function render(FinancialStatement $statement): string;
}
