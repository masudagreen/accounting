<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval\Port;

/**
 * Transport-agnostic outbound mail message.
 *
 * Adapters (`SymfonyMailSender`, `InMemoryMailSender`, `NullMailSender`) are
 * responsible for converting this into their own native representation. The
 * envelope intentionally stays narrow — we're not trying to replicate every
 * Symfony Mailer option, just the fields the approval pipeline actually uses.
 */
final readonly class MailEnvelope
{
    public function __construct(
        public string $to,
        public string $subject,
        public string $textBody,
        public string $htmlBody,
        public ?string $from = null,
        public ?string $fromName = null,
        public ?string $replyTo = null,
    ) {
    }
}
