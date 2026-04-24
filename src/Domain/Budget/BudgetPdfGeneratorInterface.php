<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

/**
 * Renders a {@see Budget} (予算書) to a PDF byte string.
 *
 * Kept as a separate port from {@see BudgetVariancePdfGeneratorInterface}
 * so each layout can evolve independently without cross-coupling the
 * domain through a single union-type renderer.
 */
interface BudgetPdfGeneratorInterface
{
    public function render(Budget $budget): string;
}
