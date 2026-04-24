<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Mail;

use Rucaro\Application\Approval\Port\MailEnvelope;
use Rucaro\Application\Approval\Port\MailSenderInterface;

/**
 * Records delivered envelopes in memory. Used by tests to assert on the
 * approval pipeline output without touching SMTP. Also useful as the default
 * adapter during local development when the operator wants to sanity-check
 * the rendered body.
 */
final class InMemoryMailSender implements MailSenderInterface
{
    /** @var list<MailEnvelope> */
    private array $sent = [];

    public function send(MailEnvelope $envelope): void
    {
        $this->sent[] = $envelope;
    }

    /**
     * @return list<MailEnvelope>
     */
    public function sent(): array
    {
        return $this->sent;
    }

    public function count(): int
    {
        return count($this->sent);
    }

    public function last(): ?MailEnvelope
    {
        return $this->sent === [] ? null : $this->sent[count($this->sent) - 1];
    }

    public function reset(): void
    {
        $this->sent = [];
    }
}
