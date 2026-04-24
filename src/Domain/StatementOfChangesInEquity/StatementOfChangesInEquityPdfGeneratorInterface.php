<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

/**
 * Renders a {@see StatementOfChangesInEquity} aggregate into a PDF
 * byte string.
 *
 * Kept as a thin port so the infrastructure-level implementation
 * ({@see \Rucaro\Infrastructure\StatementOfChangesInEquity\DompdfStatementOfChangesInEquityGenerator})
 * can swap Smarty + dompdf for a different layout engine without
 * touching the domain.
 */
interface StatementOfChangesInEquityPdfGeneratorInterface
{
    public function render(StatementOfChangesInEquity $statement): string;
}
