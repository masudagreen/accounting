<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Exception\EntityNotFoundException;

/**
 * Table-driven stub used by approval UseCase tests. Register
 * {@see ApprovalTargetInterface} instances keyed by `kind:id`; the
 * `resolve()` method returns them unchanged so tests can inspect the
 * post-approval state.
 */
final class StubApprovalTargetResolver implements ApprovalTargetResolverInterface
{
    /** @var array<string, ApprovalTargetInterface> */
    private array $targets = [];

    public function register(ApprovalTargetInterface $target): void
    {
        $this->targets[$this->key($target->kind(), $target->id())] = $target;
    }

    public function resolve(ApprovalTargetKind $kind, string $id): ApprovalTargetInterface
    {
        $key = $this->key($kind, $id);
        if (!isset($this->targets[$key])) {
            throw EntityNotFoundException::for($kind->value, $id);
        }
        return $this->targets[$key];
    }

    private function key(ApprovalTargetKind $kind, string $id): string
    {
        return $kind->value . ':' . $id;
    }
}
