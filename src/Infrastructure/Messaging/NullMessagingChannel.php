<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Messaging;

use Rucaro\Application\Approval\Port\MessagingChannelInterface;
use Rucaro\Application\Approval\Port\MessagingMessage;

/**
 * No-op adapter wired in while LINE / Slack / Discord integrations remain
 * unimplemented (Phase 5 default). Phase 6 will replace this binding with
 * channel-specific adapters without touching the port.
 */
final class NullMessagingChannel implements MessagingChannelInterface
{
    public function send(MessagingMessage $message): void
    {
        // Intentionally empty — see class docblock.
        unset($message);
    }
}
