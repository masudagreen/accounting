<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Approval;

use Rucaro\Application\Approval\Port\ApprovalNotifierInterface;
use Rucaro\Application\Approval\Port\MailEnvelope;
use Rucaro\Application\Approval\Port\MailSenderInterface;
use Rucaro\Application\Approval\Port\MessagingChannelInterface;
use Rucaro\Application\Approval\Port\MessagingMessage;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalToken;
use Smarty\Smarty;

/**
 * Dispatches approval notifications through the correct transport (mail /
 * messaging / null).
 *
 * Rendering:
 *   - Email bodies run through Smarty 5 templates under
 *     `storage/templates/mail/approval/`.
 *   - Messaging bodies use a single plaintext string since the Phase 5
 *     messaging channel is a no-op.
 *
 * Keeping template lookup local to this class means the UseCase layer stays
 * free of infrastructure concerns.
 */
final class DefaultApprovalNotifier implements ApprovalNotifierInterface
{
    public function __construct(
        private readonly MailSenderInterface $mail,
        private readonly MessagingChannelInterface $messaging,
        private readonly string $appUrl,
        private readonly string $approveUrlTemplate,
        private readonly string $rejectUrlTemplate,
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $locale = 'ja',
    ) {
    }

    public function notifyIssued(
        ApprovalToken $token,
        string $tokenPlaintext,
        ApprovalTargetInterface $target,
    ): void {
        $approveUrl = $this->expandUrl($this->approveUrlTemplate, $tokenPlaintext);
        $rejectUrl = $this->expandUrl($this->rejectUrlTemplate, $tokenPlaintext);

        $context = [
            'target' => [
                'kind'    => $target->kind()->value,
                'id'      => $target->id(),
                'summary' => $target->summary(),
                'details' => $target->details(),
            ],
            'approveUrl' => $approveUrl,
            'rejectUrl'  => $rejectUrl,
            'expiresAt'  => $token->expiresAt->format('Y-m-d H:i:s T'),
            'issuerId'   => $token->issuedByUserId,
            'appUrl'     => $this->appUrl,
            'recipient'  => $token->recipient,
        ];

        switch (true) {
            case $token->channel === ApprovalChannel::Email:
                $envelope = $this->renderMail($context);
                $this->mail->send(new MailEnvelope(
                    to: $token->recipient,
                    subject: $envelope['subject'],
                    textBody: $envelope['text'],
                    htmlBody: $envelope['html'],
                ));
                break;
            case $token->channel->isMessaging():
                $this->messaging->send(new MessagingMessage(
                    channel: $token->channel,
                    recipient: $token->recipient,
                    subject: sprintf('[Rucaro] %s', $target->summary()),
                    body: $this->renderMessagingBody($context),
                    metadata: [
                        'approve_url' => $approveUrl,
                        'reject_url'  => $rejectUrl,
                        'expires_at'  => $token->expiresAt->format(DATE_ATOM),
                    ],
                ));
                break;
            case $token->channel === ApprovalChannel::Null:
            default:
                // Intentionally ignored; the operator disabled delivery.
                break;
        }
    }

    /**
     * @param array<string, mixed> $context
     * @return array{subject:string,text:string,html:string}
     */
    private function renderMail(array $context): array
    {
        $subjectTpl = sprintf('subject.%s.tpl', $this->locale);
        $textTpl = sprintf('body.text.%s.tpl', $this->locale);
        $htmlTpl = sprintf('body.html.%s.tpl', $this->locale);

        $smarty = $this->buildSmarty();
        $smarty->assign($context);
        return [
            'subject' => trim((string) $smarty->fetch($this->resolveTemplate($subjectTpl, 'subject.ja.tpl'))),
            'text'    => (string) $smarty->fetch($this->resolveTemplate($textTpl, 'body.text.ja.tpl')),
            'html'    => (string) $smarty->fetch($this->resolveTemplate($htmlTpl, 'body.html.ja.tpl')),
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    private function renderMessagingBody(array $context): string
    {
        $target = is_array($context['target'] ?? null) ? $context['target'] : [];
        $summary = is_string($target['summary'] ?? null) ? (string) $target['summary'] : '';
        $approve = is_string($context['approveUrl'] ?? null) ? (string) $context['approveUrl'] : '';
        $reject = is_string($context['rejectUrl'] ?? null) ? (string) $context['rejectUrl'] : '';
        $expires = is_string($context['expiresAt'] ?? null) ? (string) $context['expiresAt'] : '';

        return sprintf(
            "Rucaro 承認依頼: %s\n承認: %s\n却下: %s\n期限: %s",
            $summary,
            $approve,
            $reject,
            $expires,
        );
    }

    private function buildSmarty(): Smarty
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir($this->templateDir);
        $smarty->setCompileDir($this->compileDir);
        $smarty->escape_html = true;
        return $smarty;
    }

    private function resolveTemplate(string $preferred, string $fallback): string
    {
        $preferredPath = $this->templateDir . DIRECTORY_SEPARATOR . $preferred;
        if (is_file($preferredPath)) {
            return $preferred;
        }
        return $fallback;
    }

    private function expandUrl(string $template, string $tokenPlaintext): string
    {
        $expanded = str_replace('{token}', $tokenPlaintext, $template);
        return str_replace('${APP_URL}', $this->appUrl, $expanded);
    }
}
