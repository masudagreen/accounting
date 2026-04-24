<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use Rucaro\Domain\Budget\Budget;

/**
 * Standard output envelope for Budget write UseCases.
 */
final readonly class BudgetOutput
{
    public function __construct(public Budget $budget)
    {
    }
}
