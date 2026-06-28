<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Journal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\ApproveJournalUseCase;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Http\Controller\Ui\Journal\JournalApproveController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;

/**
 * Unit coverage for {@see JournalApproveController}.
 *
 * The controller is intentionally thin (auth guard → CSRF → UseCase → flash + redirect),
 * so the tests focus on:
 *   - guard branches (no session, missing entity, invalid id, foreign entity, missing journal)
 *   - CSRF rejection
 *   - happy path (Draft → Approved + success flash + redirect to show)
 *   - illegal transition (already-Posted → flash error + redirect to show)
 */
#[CoversClass(JournalApproveController::class)]
final class JournalApproveControllerTest extends TestCase
{
    private const USER_ID = '01KPQYJQG0CPKK6QYD19JVZK0B';
    private const ENTITY_ID = '01KPQYJQG07SYYBB23EBS0VHNF';
    private const JOURNAL_ID = '01KPQYJQG0S08Y2DGQVEHTTQR3';
    private const OTHER_ENTITY_ID = '01KPQYJQG0P2143FJE7H6TNEER';

    #[\Override]
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    #[\Override]
    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRedirectsToLoginWhenSessionMissing(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft(self::JOURNAL_ID));
        [$controller] = $this->build($repo);

        $response = $controller->submit(
            $this->postRequest(''),
            self::JOURNAL_ID,
        );

        self::assertSame(303, $response->status);
        self::assertSame('/ui/login', $response->headers['Location'] ?? null);
    }

    public function testRedirectsToDashboardWhenEntityNotSelected(): void
    {
        $_SESSION[SessionStore::KEY_USER_ID] = self::USER_ID;
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft(self::JOURNAL_ID));
        [$controller, $flash] = $this->build($repo);

        $response = $controller->submit(
            $this->postRequest(''),
            self::JOURNAL_ID,
        );

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? null);
        self::assertNotEmpty($flash->current(), 'a warning flash should be queued');
    }

    public function testReturnsNotFoundWhenIdInvalid(): void
    {
        $this->seedSession();
        $repo = new InMemoryJournalRepository();
        [$controller] = $this->build($repo);

        $response = $controller->submit($this->postRequest(''), 'NOT-A-ULID');

        self::assertSame(404, $response->status);
    }

    public function testReturnsNotFoundWhenJournalMissing(): void
    {
        $this->seedSession();
        $repo = new InMemoryJournalRepository();
        [$controller] = $this->build($repo);

        $response = $controller->submit($this->postRequest(''), self::JOURNAL_ID);

        self::assertSame(404, $response->status);
    }

    public function testReturnsNotFoundWhenJournalBelongsToAnotherEntity(): void
    {
        $this->seedSession();
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft(self::JOURNAL_ID, self::OTHER_ENTITY_ID));
        [$controller] = $this->build($repo);

        $response = $controller->submit($this->postRequest(''), self::JOURNAL_ID);

        self::assertSame(404, $response->status);
    }

    public function testRedirectsBackWithErrorWhenCsrfMissing(): void
    {
        $this->seedSession();
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft(self::JOURNAL_ID));
        [$controller, $flash] = $this->build($repo);

        $response = $controller->submit(
            $this->postRequest('_csrf=bogus'),
            self::JOURNAL_ID,
        );

        self::assertSame(303, $response->status);
        self::assertSame('/ui/journals/'.self::JOURNAL_ID, $response->headers['Location'] ?? null);
        $flashes = $flash->current();
        self::assertNotEmpty($flashes);
        self::assertSame(FlashMessageBag::KIND_ERROR, $flashes[0]['kind']);
    }

    public function testApprovesDraftAndRedirectsToShow(): void
    {
        $this->seedSession();
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft(self::JOURNAL_ID));
        [$controller, $flash, $csrf] = $this->build($repo);

        $token = $csrf->generateToken(JournalApproveController::CSRF_FORM_ID);
        $response = $controller->submit(
            $this->postRequest('_csrf='.$token),
            self::JOURNAL_ID,
        );

        self::assertSame(303, $response->status);
        self::assertSame('/ui/journals/'.self::JOURNAL_ID, $response->headers['Location'] ?? null);

        $reloaded = $repo->findById(self::JOURNAL_ID);
        self::assertNotNull($reloaded);
        self::assertSame(JournalStatus::Approved, $reloaded->statusEnum());
        self::assertSame(self::USER_ID, $reloaded->approvedBy);

        $flashes = $flash->current();
        self::assertNotEmpty($flashes);
        self::assertSame(FlashMessageBag::KIND_SUCCESS, $flashes[0]['kind']);
    }

    public function testIllegalTransitionFlashesErrorAndRedirects(): void
    {
        $this->seedSession();
        $repo = new InMemoryJournalRepository();

        $posted = $this->draft(self::JOURNAL_ID)
            ->approve(new \DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1')
            ->post(new \DateTimeImmutable('2026-04-21T12:20:00Z'), 'U1');
        $repo->save($posted);

        [$controller, $flash, $csrf] = $this->build($repo);
        $token = $csrf->generateToken(JournalApproveController::CSRF_FORM_ID);

        $response = $controller->submit(
            $this->postRequest('_csrf='.$token),
            self::JOURNAL_ID,
        );

        self::assertSame(303, $response->status);
        self::assertSame('/ui/journals/'.self::JOURNAL_ID, $response->headers['Location'] ?? null);

        // Status should remain Posted (no save happened on the failure path).
        $reloaded = $repo->findById(self::JOURNAL_ID);
        self::assertNotNull($reloaded);
        self::assertSame(JournalStatus::Posted, $reloaded->statusEnum());

        $flashes = $flash->current();
        self::assertNotEmpty($flashes);
        self::assertSame(FlashMessageBag::KIND_ERROR, $flashes[0]['kind']);
    }

    private function seedSession(): void
    {
        $_SESSION[SessionStore::KEY_USER_ID] = self::USER_ID;
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = self::ENTITY_ID;
    }

    /**
     * @return array{0: JournalApproveController, 1: FlashMessageBag, 2: CsrfTokenManager, 3: JournalRepositoryInterface}
     */
    private function build(JournalRepositoryInterface $repo): array
    {
        $clock = new FrozenClock();
        $session = new SessionStore();
        $csrf = new CsrfTokenManager($clock);
        $flash = new FlashMessageBag();

        $controller = new JournalApproveController(
            approveJournal: new ApproveJournalUseCase($repo, $clock),
            journals: $repo,
            session: $session,
            csrf: $csrf,
            flash: $flash,
        );

        return [$controller, $flash, $csrf, $repo];
    }

    private function postRequest(string $body): ServerRequest
    {
        return new ServerRequest(
            method: 'POST',
            path: '/ui/journals/'.self::JOURNAL_ID.'/approve',
            headers: ['content-type' => 'application/x-www-form-urlencoded'],
            query: [],
            json: null,
            rawBody: $body,
        );
    }

    private function draft(string $id, string $entityId = self::ENTITY_ID): Journal
    {
        $lines = [
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE00001',
                lineNo: 1,
                side: 'debit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
                subAccountTitleId: null,
                amount: '500.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new \DateTimeImmutable('2026-04-21T12:00:00Z'),
            ),
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE00002',
                lineNo: 2,
                side: 'credit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL002',
                subAccountTitleId: null,
                amount: '500.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new \DateTimeImmutable('2026-04-21T12:00:00Z'),
            ),
        ];

        return new Journal(
            id: $id,
            entityId: $entityId,
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new \DateTimeImmutable('2026-04-21'),
            bookedAt: new \DateTimeImmutable('2026-04-21T12:00:00Z'),
            summary: 'Draft',
            totalAmount: '500.0000',
            currencyCode: 'JPY',
            status: 'draft',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: self::USER_ID,
            approvedBy: null,
            approvedAt: null,
            createdAt: new \DateTimeImmutable('2026-04-21T12:00:00Z'),
            updatedAt: new \DateTimeImmutable('2026-04-21T12:00:00Z'),
            deletedAt: null,
            lines: $lines,
        );
    }
}
