<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Mail;

use RuntimeException;
use Rucaro\Application\Approval\Port\MailEnvelope;
use Rucaro\Application\Approval\Port\MailSenderInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Production adapter backed by `symfony/mailer`.
 *
 * Construction options:
 *   - `$mailer` override: tests can inject a custom `MailerInterface`.
 *   - `$dsn` fallback: production wiring builds the transport via
 *     `Transport::fromDsn()` lazily on first send so missing DSN values
 *     fail loudly rather than at bootstrap time.
 */
final class SymfonyMailSender implements MailSenderInterface
{
    public function __construct(
        private readonly string $dsn,
        private readonly string $fromAddress,
        private readonly string $fromName = '',
        private ?MailerInterface $mailer = null,
    ) {
    }

    public function send(MailEnvelope $envelope): void
    {
        $mailer = $this->mailer ?? $this->buildMailer();
        $email = new Email();
        $from = $envelope->from ?? $this->fromAddress;
        if ($from === '') {
            throw new RuntimeException('SymfonyMailSender requires a from address (env MAIL_FROM).');
        }
        $fromName = $envelope->fromName ?? $this->fromName;
        $email->from(new Address($from, $fromName));
        $email->to(new Address($envelope->to));
        if ($envelope->replyTo !== null && $envelope->replyTo !== '') {
            $email->replyTo(new Address($envelope->replyTo));
        }
        $email->subject($envelope->subject);
        $email->text($envelope->textBody);
        if ($envelope->htmlBody !== '') {
            $email->html($envelope->htmlBody);
        }
        $mailer->send($email);
    }

    private function buildMailer(): MailerInterface
    {
        if ($this->dsn === '') {
            throw new RuntimeException('SymfonyMailSender requires a MAIL_DSN value.');
        }
        $transport = Transport::fromDsn($this->dsn);
        $mailer = new Mailer($transport);
        $this->mailer = $mailer;
        return $mailer;
    }
}
