<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

/**
 * Renders a {@see BudgetVarianceAnalysis} (予実対比表) to a PDF byte
 * string.
 */
interface BudgetVariancePdfGeneratorInterface
{
    public function render(BudgetVarianceAnalysis $analysis): string;
}
