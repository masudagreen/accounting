<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval\Port;

/**
 * Hexagonal port for delivering a {@see MailEnvelope} to a mail transport.
 *
 * Production wiring uses `SymfonyMailSender`. Tests wire `InMemoryMailSender`
 * so assertions can inspect the emitted envelopes without a live SMTP server.
 * Ops installations without outbound mail can wire `NullMailSender` to keep
 * the pipeline running as a no-op.
 */
interface MailSenderInterface
{
    public function send(MailEnvelope $envelope): void;
}
