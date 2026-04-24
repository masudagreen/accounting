<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnForm;

/**
 * Standard output envelope for Blue Return write UseCases.
 */
final readonly class BlueReturnOutput
{
    public function __construct(public BlueReturnForm $form)
    {
    }
}
