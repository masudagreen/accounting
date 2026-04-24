<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Application\Journal\SearchJournalUseCase;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Domain\Journal\ValueObject\JournalDate;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/journals — paginated, sortable, filterable journal list.
 *
 * Filtering shape matches the task spec in Phase 7-2:
 *   year + month (converted to a date window server-side),
 *   accountTitleId, status, free-text `q`.
 *
 * Sort is pinned to the criteria allow-list so the user can only request
 * a column we actually indexed.
 */
final readonly class JournalListController
{
    /** @var list<int> */
    private const PAGE_SIZES = [25, 50, 100];
    private const DEFAULT_PAGE_SIZE = 25;
    private const STATUS_FILTERS = ['draft', 'pending_approval', 'approved', 'rejected', 'posted'];

    public function __construct(
        private SearchJournalUseCase $search,
        private JournalUiContext $uiContext,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function invoke(ServerRequest $request): HtmlResponse
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $page     = $request->positiveInt('page', 1, 1);
        $pageSize = $this->resolvePageSize($request->queryString('pageSize'));
        [$sortBy, $sortOrder] = JournalSearchCriteria::resolveSort(
            $request->queryString('sortBy'),
            $request->queryString('sortOrder'),
        );

        $year    = $this->intOrNull($request->queryString('year'));
        $month   = $this->intOrNull($request->queryString('month'));
        $account = $request->queryString('accountTitleId');
        $status  = $this->resolveStatus($request->queryString('status'));
        $query   = $request->queryString('q');

        [$from, $to] = self::yearMonthToRange($year, $month);

        $criteria = new JournalSearchCriteria(
            entityId: $entityId,
            page: $page,
            pageSize: $pageSize,
            from: $from,
            to: $to,
            fiscalTermId: $this->session->getSelectedFiscalTerm(),
            accountTitleId: $account,
            status: $status,
            source: null,
            textQuery: $query,
            includeTrashed: false,
            sortBy: $sortBy,
            sortOrder: $sortOrder,
        );

        try {
            $result = $this->search->execute($criteria);
            $items = array_map(
                static fn (Journal $j): array => [
                    'id'          => $j->id,
                    'journalDate' => $j->journalDate->format('Y-m-d'),
                    'summary'     => $j->summary,
                    'totalAmount' => $j->totalAmount,
                    'status'      => $j->status,
                    'createdBy'   => $j->createdBy,
                    'createdAt'   => $j->createdAt->format('Y-m-d H:i'),
                ],
                $result->items,
            );
            $total = $result->total;
        } catch (\Throwable $e) {
            $this->flash->addError('仕訳一覧の取得に失敗しました: ' . $e->getMessage());
            $items = [];
            $total = 0;
        }

        $accountTitles = $this->uiContext->accountTitlesForEntity($entityId);
        $fiscalTerms   = $this->uiContext->fiscalTermsForEntity($entityId);

        $data = [
            'page_title'         => '仕訳一覧',
            'active_nav'         => 'journals',
            'csrf_logout_token'  => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'  => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'  => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'  => EntitySwitchController::CSRF_FORM_ID,
            'display_name'       => $this->session->getDisplayName() ?? '',
            'user_email'         => $this->session->getEmail() ?? '',
            'entities'           => [],
            'selected_entity_id' => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'     => $this->flash->consume(),
            'items'              => $items,
            'total'              => $total,
            'page'               => $page,
            'page_size'          => $pageSize,
            'page_sizes'         => self::PAGE_SIZES,
            'total_pages'        => (int) max(1, (int) ceil($total / max(1, $pageSize))),
            'sort_by'            => $sortBy,
            'sort_order'         => $sortOrder,
            'filter_year'        => $year !== null ? (string) $year : '',
            'filter_month'       => $month !== null ? (string) $month : '',
            'filter_account'     => $account ?? '',
            'filter_status'      => $status?->value ?? '',
            'filter_q'           => $query ?? '',
            'status_options'     => self::STATUS_FILTERS,
            'account_titles'     => $accountTitles,
            'fiscal_terms'       => $fiscalTerms,
            'year_options'       => self::buildYearOptions($fiscalTerms),
            'query_string_base'  => self::buildQueryBase($page, $pageSize, $sortBy, $sortOrder, $year, $month, $account, $status?->value, $query),
        ];
        unset($userId, $request);

        return HtmlResponse::ok($this->view->render('journals/list.html.tpl', $data));
    }

    private function resolvePageSize(?string $raw): int
    {
        if ($raw === null) {
            return self::DEFAULT_PAGE_SIZE;
        }
        $n = (int) $raw;
        return in_array($n, self::PAGE_SIZES, true) ? $n : self::DEFAULT_PAGE_SIZE;
    }

    private function resolveStatus(?string $raw): ?JournalStatus
    {
        if ($raw === null || $raw === '' || $raw === 'all') {
            return null;
        }
        foreach (JournalStatus::cases() as $case) {
            if ($case->value === $raw) {
                return $case;
            }
        }
        return null;
    }

    private function intOrNull(?string $raw): ?int
    {
        if ($raw === null || $raw === '' || !ctype_digit($raw)) {
            return null;
        }
        return (int) $raw;
    }

    /**
     * @return array{0: ?JournalDate, 1: ?JournalDate}
     */
    public static function yearMonthToRange(?int $year, ?int $month): array
    {
        if ($year === null) {
            return [null, null];
        }
        $utc = new DateTimeZone('UTC');
        if ($month !== null && $month >= 1 && $month <= 12) {
            $from = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month), $utc);
            $to = $from->modify('last day of this month');
        } else {
            $from = new DateTimeImmutable(sprintf('%04d-01-01', $year), $utc);
            $to = new DateTimeImmutable(sprintf('%04d-12-31', $year), $utc);
        }
        return [new JournalDate($from), new JournalDate($to)];
    }

    /**
     * Build the "year" select options from the entity's fiscal terms. Falls
     * back to "current year ± 3" when the entity has no terms yet.
     *
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $fiscalTerms
     * @return list<int>
     */
    private static function buildYearOptions(array $fiscalTerms): array
    {
        $years = [];
        foreach ($fiscalTerms as $t) {
            if ($t['startDate'] !== '' && preg_match('/^(\d{4})/', $t['startDate'], $m) === 1) {
                $years[(int) $m[1]] = true;
            }
            if ($t['endDate'] !== '' && preg_match('/^(\d{4})/', $t['endDate'], $m) === 1) {
                $years[(int) $m[1]] = true;
            }
        }
        if ($years === []) {
            $now = (int) date('Y');
            for ($y = $now - 3; $y <= $now + 1; $y++) {
                $years[$y] = true;
            }
        }
        $keys = array_keys($years);
        sort($keys);
        /** @var list<int> */
        return array_values(array_reverse($keys));
    }

    private static function buildQueryBase(
        int $page,
        int $pageSize,
        string $sortBy,
        string $sortOrder,
        ?int $year,
        ?int $month,
        ?string $account,
        ?string $status,
        ?string $query,
    ): string {
        unset($page); // page never belongs in the base — each link re-injects it
        $parts = [
            'pageSize'  => (string) $pageSize,
            'sortBy'    => $sortBy,
            'sortOrder' => $sortOrder,
        ];
        if ($year !== null) {
            $parts['year'] = (string) $year;
        }
        if ($month !== null) {
            $parts['month'] = (string) $month;
        }
        if ($account !== null && $account !== '') {
            $parts['accountTitleId'] = $account;
        }
        if ($status !== null && $status !== '') {
            $parts['status'] = $status;
        }
        if ($query !== null && $query !== '') {
            $parts['q'] = $query;
        }
        return http_build_query($parts);
    }
}
