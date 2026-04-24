<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval;

/**
 * Channels through which an approval request may be delivered.
 *
 * The string values align with the `approval_tokens.channel` CHECK constraint
 * (ADR-002 §approval_tokens). `Null` is Phase 5's default when the operator
 * wants to prototype the workflow without a live mail transport.
 */
enum ApprovalChannel: string
{
    case Email = 'email';
    case Line = 'line';
    case Slack = 'slack';
    case Discord = 'discord';
    case Null = 'null';

    /**
     * Whether this channel is delivered by a {@see \Rucaro\Application\Approval\Port\MailSenderInterface}.
     */
    public function isMail(): bool
    {
        return $this === self::Email;
    }

    /**
     * Whether this channel is delivered by a {@see \Rucaro\Application\Approval\Port\MessagingChannelInterface}.
     */
    public function isMessaging(): bool
    {
        return $this === self::Line || $this === self::Slack || $this === self::Discord;
    }

    public function isNoop(): bool
    {
        return $this === self::Null;
    }
}
