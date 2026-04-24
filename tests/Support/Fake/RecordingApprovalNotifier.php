<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Application\Approval\Port\ApprovalNotifierInterface;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalToken;

/**
 * Records every `notifyIssued()` call so tests can assert that the pipeline
 * attempted delivery. Intentionally does not render any template — the
 * rendering logic is exercised by DefaultApprovalNotifierTest directly.
 */
final class RecordingApprovalNotifier implements ApprovalNotifierInterface
{
    /** @var list<array{token: ApprovalToken, plaintext: string, target: ApprovalTargetInterface}> */
    public array $calls = [];

    public function notifyIssued(
        ApprovalToken $token,
        string $tokenPlaintext,
        ApprovalTargetInterface $target,
    ): void {
        $this->calls[] = [
            'token'     => $token,
            'plaintext' => $tokenPlaintext,
            'target'    => $target,
        ];
    }
}
