<?php

declare(strict_types=1);

namespace Rucaro\Domain\BreakEvenPoint;

/**
 * Port for rendering a {@see BreakEvenPointAnalysis} to PDF.
 */
interface BreakEvenPointPdfGeneratorInterface
{
    public function render(BreakEvenPointAnalysis $analysis): string;
}
