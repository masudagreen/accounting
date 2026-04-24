<?php

declare(strict_types=1);

namespace Rucaro\Tests\E2E;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Approval\FindApprovalByTokenUseCase;
use Rucaro\Application\Approval\IssueApprovalTokenUseCase;
use Rucaro\Application\Approval\IssueApprovalTokenUseCaseInput;
use Rucaro\Application\Approval\RespondToApprovalUseCase;
use Rucaro\Application\Approval\RespondToApprovalUseCaseInput;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\Service\JournalApprovalTarget;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Infrastructure\Approval\DefaultApprovalNotifier;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Infrastructure\Mail\InMemoryMailSender;
use Rucaro\Infrastructure\Messaging\NullMessagingChannel;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryApprovalTokenRepository;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;
use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;

/**
 * End-to-end smoke for the Phase 5.2 approval pipeline.
 *
 * Wires the UseCases to real in-memory adapters and exercises the full
 * issue → notify → inspect → respond → persist flow against a Journal
 * draft. Keeps the suite free of Docker / SMTP dependencies so CI can
 * treat this as a guard against integration regressions.
 */
#[CoversNothing]
#[Group('e2e')]
final class ApprovalPipelineSmokeTest extends TestCase
{
    public function testApprovalHappyPathTransitionsJournalToApproved(): void
    {
        $fixture = $this->wire();

        $issueOutput = $fixture['issue']->execute(new IssueApprovalTokenUseCaseInput(
            targetKind: ApprovalTargetKind::Journal,
            targetId: $fixture['journalId'],
            channel: ApprovalChannel::Email,
            recipient: 'reviewer@example.com',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
            ttlHours: 72,
        ));

        self::assertCount(1, $fixture['mail']->sent());
        self::assertStringContainsString($issueOutput->tokenPlaintext, $fixture['mail']->last()?->textBody ?? '');

        $find = $fixture['find']->execute($issueOutput->tokenPlaintext);
        self::assertSame('active', $find->status);

        $respond = $fixture['respond']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $issueOutput->tokenPlaintext,
            decision: ApprovalDecision::Approved,
            responseDetail: 'approved via smoke test',
            actorUserId: '01HW7K9B2QV7C8Y4ZUSER000099',
        ));

        self::assertSame(ApprovalDecision::Approved, $respond->token->decision);
        $persisted = $fixture['journals']->findById($fixture['journalId']);
        self::assertNotNull($persisted);
        self::assertSame('approved', $persisted->status);
    }

    public function testRejectionPathTransitionsJournalToRejected(): void
    {
        $fixture = $this->wire();

        $issueOutput = $fixture['issue']->execute(new IssueApprovalTokenUseCaseInput(
            targetKind: ApprovalTargetKind::Journal,
            targetId: $fixture['journalId'],
            channel: ApprovalChannel::Email,
            recipient: 'reviewer@example.com',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
        ));

        $fixture['respond']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $issueOutput->tokenPlaintext,
            decision: ApprovalDecision::Rejected,
            responseDetail: 'missing receipt',
            actorUserId: '01HW7K9B2QV7C8Y4ZUSER000099',
        ));

        $persisted = $fixture['journals']->findById($fixture['journalId']);
        self::assertNotNull($persisted);
        self::assertSame('rejected', $persisted->status);
        self::assertStringContainsString('[REJECTED:missing receipt]', $persisted->summary);
    }

    /**
     * @return array{
     *     issue:IssueApprovalTokenUseCase,
     *     find:FindApprovalByTokenUseCase,
     *     respond:RespondToApprovalUseCase,
     *     journals:JournalRepositoryInterface,
     *     mail:InMemoryMailSender,
     *     journalId:string,
     * }
     */
    private function wire(): array
    {
        $tz = new DateTimeZone('UTC');
        $clock = new FrozenClock('2026-04-21T12:00:00.000Z');
        $journals = new InMemoryJournalRepository();
        $journal = $this->makeJournal($tz);
        $journals->save($journal);

        $resolver = new class ($journals) implements ApprovalTargetResolverInterface {
            public function __construct(private readonly JournalRepositoryInterface $journals) {}
            public function resolve(ApprovalTargetKind $kind, string $id): ApprovalTargetInterface
            {
                if ($kind !== ApprovalTargetKind::Journal) {
                    throw new \InvalidArgumentException('only journal supported');
                }
                $j = $this->journals->findById($id);
                if ($j === null) {
                    throw \Rucaro\Domain\Exception\EntityNotFoundException::for('Journal', $id);
                }
                return new JournalApprovalTarget($j, $this->journals);
            }
        };

        $mail = new InMemoryMailSender();
        $messaging = new NullMessagingChannel();
        $repoRoot = dirname(__DIR__, 2);
        $templateDir = $repoRoot . '/storage/templates/mail/approval';
        $compileDir = $repoRoot . '/storage/cache/smarty_compile';
        if (!is_dir($compileDir)) {
            @mkdir($compileDir, 0775, true);
        }
        $notifier = new DefaultApprovalNotifier(
            mail: $mail,
            messaging: $messaging,
            appUrl: 'http://localhost:8080',
            approveUrlTemplate: 'http://localhost:8080/api/v1/approvals/?token={token}&decision=approved',
            rejectUrlTemplate: 'http://localhost:8080/api/v1/approvals/?token={token}&decision=rejected',
            templateDir: $templateDir,
            compileDir: $compileDir,
        );

        $tokens = new InMemoryApprovalTokenRepository();
        $issue = new IssueApprovalTokenUseCase(
            tokens: $tokens,
            targets: $resolver,
            notifier: $notifier,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );
        $find = new FindApprovalByTokenUseCase($tokens, $resolver, $clock);
        $respond = new RespondToApprovalUseCase($tokens, $resolver, $clock);

        return [
            'issue'    => $issue,
            'find'     => $find,
            'respond'  => $respond,
            'journals' => $journals,
            'mail'     => $mail,
            'journalId' => $journal->id,
        ];
    }

    private function makeJournal(DateTimeZone $tz): Journal
    {
        $ts = new DateTimeImmutable('2026-04-21T12:00:00Z', $tz);
        $lines = [
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINEE2EAAA',
                lineNo: 1,
                side: 'debit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
                subAccountTitleId: null,
                amount: '500.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: $ts,
            ),
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINEE2EBBB',
                lineNo: 2,
                side: 'credit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL002',
                subAccountTitleId: null,
                amount: '500.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: $ts,
            ),
        ];

        return new Journal(
            id: '01HW7K9B2QV7C8Y4ZJRNLE2E001',
            entityId: '01HW7K9B2QV7C8Y4ZENTITY0001',
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21', $tz),
            bookedAt: $ts,
            summary: 'E2E approval smoke',
            totalAmount: '500.0000',
            currencyCode: 'JPY',
            status: 'draft',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedBy: null,
            approvedAt: null,
            createdAt: $ts,
            updatedAt: $ts,
            deletedAt: null,
            lines: $lines,
        );
    }
}
