<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Approval\Port\MessagingChannelInterface;
use Rucaro\Application\Approval\Port\MessagingMessage;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Infrastructure\Approval\DefaultApprovalNotifier;
use Rucaro\Infrastructure\Mail\InMemoryMailSender;
use Rucaro\Tests\Support\Fake\FakeApprovalTarget;

#[CoversClass(DefaultApprovalNotifier::class)]
final class DefaultApprovalNotifierTest extends TestCase
{
    public function testEmailChannelRendersMailBodyWithBothUrls(): void
    {
        $mail = new InMemoryMailSender();
        $messaging = new class implements MessagingChannelInterface {
            /** @var list<MessagingMessage> */
            public array $sent = [];
            public function send(MessagingMessage $message): void
            {
                $this->sent[] = $message;
            }
        };
        $notifier = $this->notifier($mail, $messaging);

        $token = $this->token(ApprovalChannel::Email);
        $target = new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
            summary: 'Office supplies',
        );

        $notifier->notifyIssued($token, 'plaintext-token', $target);

        self::assertCount(1, $mail->sent());
        self::assertCount(0, $messaging->sent);
        $envelope = $mail->last();
        self::assertNotNull($envelope);
        self::assertSame('reviewer@example.com', $envelope->to);
        self::assertStringContainsString('Office supplies', $envelope->subject);
        self::assertStringContainsString('plaintext-token', $envelope->textBody);
        self::assertStringContainsString('decision=approved', $envelope->textBody);
        self::assertStringContainsString('decision=rejected', $envelope->textBody);
    }

    public function testMessagingChannelRoutesToMessagingAdapter(): void
    {
        $mail = new InMemoryMailSender();
        $messaging = new class implements MessagingChannelInterface {
            /** @var list<MessagingMessage> */
            public array $sent = [];
            public function send(MessagingMessage $message): void
            {
                $this->sent[] = $message;
            }
        };
        $notifier = $this->notifier($mail, $messaging);

        $token = $this->token(ApprovalChannel::Slack);
        $target = new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
            summary: 'Lunch',
        );

        $notifier->notifyIssued($token, 'plaintext-token', $target);

        self::assertCount(0, $mail->sent());
        self::assertCount(1, $messaging->sent);
        self::assertSame(ApprovalChannel::Slack, $messaging->sent[0]->channel);
        self::assertStringContainsString('Lunch', $messaging->sent[0]->body);
        $approveUrl = self::stringOrEmpty($messaging->sent[0]->metadata['approve_url'] ?? '');
        self::assertSame('plaintext-token', self::tokenFromUrl($approveUrl));
    }

    public function testNullChannelIsNoop(): void
    {
        $mail = new InMemoryMailSender();
        $messaging = new class implements MessagingChannelInterface {
            /** @var list<MessagingMessage> */
            public array $sent = [];
            public function send(MessagingMessage $message): void
            {
                $this->sent[] = $message;
            }
        };
        $notifier = $this->notifier($mail, $messaging);

        $token = $this->token(ApprovalChannel::Null);
        $target = new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
        );

        $notifier->notifyIssued($token, 'plaintext-token', $target);

        self::assertCount(0, $mail->sent());
        self::assertCount(0, $messaging->sent);
    }

    private function notifier(InMemoryMailSender $mail, MessagingChannelInterface $messaging): DefaultApprovalNotifier
    {
        $repoRoot = dirname(__DIR__, 4);
        $templateDir = $repoRoot . '/storage/templates/mail/approval';
        $compileDir = $repoRoot . '/storage/cache/smarty_compile';
        if (!is_dir($compileDir)) {
            @mkdir($compileDir, 0775, true);
        }
        return new DefaultApprovalNotifier(
            mail: $mail,
            messaging: $messaging,
            appUrl: 'http://localhost:8080',
            approveUrlTemplate: 'http://localhost:8080/api/v1/approvals/?token={token}&decision=approved',
            rejectUrlTemplate: 'http://localhost:8080/api/v1/approvals/?token={token}&decision=rejected',
            templateDir: $templateDir,
            compileDir: $compileDir,
        );
    }

    private function token(ApprovalChannel $channel): ApprovalToken
    {
        $tz = new DateTimeZone('UTC');
        $issued = new DateTimeImmutable('2026-04-21T12:00:00Z', $tz);
        $expires = new DateTimeImmutable('2026-04-24T12:00:00Z', $tz);
        return new ApprovalToken(
            id: '01HW7K9B2QV7C8Y4ZAPPR0000001',
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            tokenHash: str_repeat('a', 64),
            tokenPrefix: '0123456789abcdef',
            channel: $channel,
            recipient: 'reviewer@example.com',
            issuedAt: $issued,
            expiresAt: $expires,
            respondedAt: null,
            decision: null,
            responseDetail: '',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
            createdAt: $issued,
            updatedAt: $issued,
        );
    }

    private static function stringOrEmpty(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }

    private static function tokenFromUrl(string $url): string
    {
        $q = parse_url($url, PHP_URL_QUERY);
        if (!is_string($q) || $q === '') {
            return '';
        }
        parse_str($q, $parts);
        return is_string($parts['token'] ?? null) ? (string) $parts['token'] : '';
    }
}
