<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval\Port;

use Rucaro\Domain\Approval\ApprovalChannel;

/**
 * Outbound message for non-email channels (LINE / Slack / Discord / …).
 *
 * `metadata` carries channel-specific payload such as block-kit or card JSON.
 * Phase 5's `NullMessagingChannel` ignores these entirely; Phase 6 adapters
 * will translate them into their SDK objects.
 */
final readonly class MessagingMessage
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public ApprovalChannel $channel,
        public string $recipient,
        public string $subject,
        public string $body,
        public array $metadata = [],
    ) {
    }
}
