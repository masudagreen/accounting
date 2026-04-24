<?php

declare(strict_types=1);

namespace Rucaro\Domain\BlueReturn;

/**
 * Renders a {@see BlueReturnForm} to a PDF byte string.
 *
 * The output is a 4-page A4 document matching the layout of the
 * 税務署-issued 青色申告決算書.
 */
interface BlueReturnPdfGeneratorInterface
{
    public function render(BlueReturnForm $form): string;
}
