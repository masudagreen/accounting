<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Multi;

use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;

/**
 * PDF generator seam for the multi-period (Wave 6-I) FS.
 *
 * Sits alongside {@see \Rucaro\Domain\FinancialStatement\FinancialStatementGeneratorInterface}
 * but takes the multi aggregate. Kept interface-separated so tests or
 * alternative print backends can swap implementations without dragging in the
 * single-period contract.
 */
interface MultiPeriodFinancialStatementGeneratorInterface
{
    public function render(MultiPeriodFinancialStatement $statement): string;

    /**
     * Renderer-specific HTML output, exposed for unit tests / snapshot diffs
     * without booting dompdf.
     */
    public function renderHtml(MultiPeriodFinancialStatement $statement): string;
}
