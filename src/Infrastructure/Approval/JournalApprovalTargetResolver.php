<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Approval;

use InvalidArgumentException;
use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\Service\JournalApprovalTarget;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Journal\JournalRepositoryInterface;

/**
 * Phase 5 resolver — only supports {@see ApprovalTargetKind::Journal}.
 *
 * Phase 6 will introduce a composed resolver that also handles
 * {@see ApprovalTargetKind::Receipt}; until then, calling resolve() with
 * a Receipt kind raises InvalidArgumentException so the regression is
 * caught immediately instead of silently falling through.
 */
final class JournalApprovalTargetResolver implements ApprovalTargetResolverInterface
{
    public function __construct(
        private readonly JournalRepositoryInterface $journals,
    ) {
    }

    public function resolve(ApprovalTargetKind $kind, string $id): ApprovalTargetInterface
    {
        if ($kind !== ApprovalTargetKind::Journal) {
            throw new InvalidArgumentException(sprintf(
                'JournalApprovalTargetResolver cannot resolve target kind %s; Phase 6 will add Receipt support.',
                $kind->value,
            ));
        }
        $journal = $this->journals->findById($id);
        if ($journal === null) {
            throw EntityNotFoundException::for('Journal', $id);
        }
        return new JournalApprovalTarget($journal, $this->journals);
    }
}
