<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval\Port;

use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalToken;

/**
 * Dispatches a newly-issued approval token over its configured channel.
 *
 * The implementation is responsible for:
 *   - Rendering the channel-appropriate body (Smarty template for mail,
 *     short text for messaging).
 *   - Picking between {@see MailSenderInterface} and
 *     {@see MessagingChannelInterface} based on {@see ApprovalToken::$channel}.
 *   - Embedding the one-time plaintext URL. The plaintext must never be
 *     persisted or logged.
 */
interface ApprovalNotifierInterface
{
    public function notifyIssued(
        ApprovalToken $token,
        string $tokenPlaintext,
        ApprovalTargetInterface $target,
    ): void;
}
