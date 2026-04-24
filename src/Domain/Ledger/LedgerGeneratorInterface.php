<?php

declare(strict_types=1);

namespace Rucaro\Domain\Ledger;

/**
 * Port for producing a binary representation of a {@see Ledger}.
 *
 * The default implementation (Infrastructure layer) renders Smarty HTML
 * and runs it through dompdf. Additional adapters (CSV, Excel, JSON)
 * would implement this interface without touching the Domain layer.
 */
interface LedgerGeneratorInterface
{
    /**
     * Render the ledger as PDF and return the raw bytes.
     */
    public function render(Ledger $ledger): string;
}
