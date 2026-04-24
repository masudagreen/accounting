<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval\Port;

/**
 * Hexagonal port for delivering a {@see MessagingMessage} to a chat platform.
 *
 * Phase 5 only ships {@see \Rucaro\Infrastructure\Messaging\NullMessagingChannel}
 * — the LINE / Slack / Discord adapters arrive in Phase 6 without touching
 * this interface.
 */
interface MessagingChannelInterface
{
    public function send(MessagingMessage $message): void;
}
