<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval\Exception;

use Rucaro\Domain\Exception\DomainException;

/**
 * Root of the approval-workflow exception hierarchy. Kept abstract so callers
 * can `catch (ApprovalException $e)` for any approval-specific failure while
 * still being able to disambiguate via the concrete subclasses below.
 */
abstract class ApprovalException extends DomainException
{
}
