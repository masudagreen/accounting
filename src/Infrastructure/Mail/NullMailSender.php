<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Mail;

use Rucaro\Application\Approval\Port\MailEnvelope;
use Rucaro\Application\Approval\Port\MailSenderInterface;

/**
 * No-op adapter wired in when `.env` has `MAIL_MAILER=null`.
 *
 * The approval workflow still records the token in the DB so operators can
 * inspect it from the admin console even when no SMTP transport is
 * configured. Useful for local-only installations.
 */
final class NullMailSender implements MailSenderInterface
{
    public function send(MailEnvelope $envelope): void
    {
        // Intentionally empty.
        unset($envelope);
    }
}
