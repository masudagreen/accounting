<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Budget;

use Rucaro\Application\Budget\BudgetLineItemInput;
use Rucaro\Application\Budget\CreateBudgetInput;
use Rucaro\Application\Budget\CreateBudgetUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET  /ui/budgets/new — empty budget form with 12-month grid.
 * POST /ui/budgets/new — create a Draft budget.
 */
final readonly class BudgetNewController
{
    public const CSRF_FORM_ID = 'ui_budget_new';

    public function __construct(
        private CreateBudgetUseCase $createBudget,
        private PlanningUiContext $ctx,
        private ClockInterface $clock,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request): HtmlResponse
    {
        unset($request);
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        $terms = $this->ctx->fiscalTermsForEntity($entityId);
        $default = PlanningUiContext::defaultFiscalTermId($terms, $this->clock->getCurrentTime());
        return $this->renderForm(
            entityId: $entityId,
            name: '',
            notes: '',
            fiscalTermId: $default ?? ($this->session->getSelectedFiscalTerm() ?? ''),
            lines: [self::blankLine()],
            errors: [],
            status: 200,
        );
    }

    public function submit(ServerRequest $request): HtmlResponse
    {
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        /** @var string $userId */
        $userId = $this->session->getUserId();

        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/budgets/new');
        }

        $name         = PlanningFormSupport::str($body, 'name');
        $notes        = PlanningFormSupport::str($body, 'notes');
        $fiscalTermId = PlanningFormSupport::str($body, 'fiscal_term_id');

        $rawLines = $body['lines'] ?? null;
        $formLines = [];
        $lineInputs = [];
        $sortOrder = 0;
        if (is_array($rawLines)) {
            foreach ($rawLines as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $accountId = PlanningFormSupport::str($row, 'account_title_id');
                $monthlyRaw = $row['monthly'] ?? null;
                /** @var list<string> $monthly */
                $monthly = [];
                $hasAmount = false;
                for ($i = 0; $i < 12; $i++) {
                    $v = is_array($monthlyRaw) ? ($monthlyRaw[$i] ?? null) : null;
                    $amt = PlanningFormSupport::normalizeAmount(is_string($v) ? $v : '');
                    $monthly[] = $amt;
                    if ($amt !== '0.0000') {
                        $hasAmount = true;
                    }
                }
                $memo = PlanningFormSupport::str($row, 'memo');
                if ($accountId === '' && !$hasAmount && $memo === '') {
                    continue;
                }
                $formLines[] = [
                    'account_title_id' => $accountId,
                    'monthly'          => $monthly,
                    'memo'             => $memo,
                ];
                if ($accountId !== '') {
                    $lineInputs[] = new BudgetLineItemInput(
                        accountTitleId: $accountId,
                        subAccountTitleId: null,
                        sortOrder: $sortOrder++,
                        monthlyAmounts: $monthly,
                        memo: $memo === '' ? null : $memo,
                    );
                }
            }
        }
        if ($formLines === []) {
            $formLines[] = self::blankLine();
        }

        $errors = [];
        if ($name === '') {
            $errors['name'] = ['予算名を入力してください。'];
        }
        if ($fiscalTermId === '') {
            $errors['fiscal_term_id'] = ['会計期間を選択してください。'];
        }
        if ($lineInputs === []) {
            $errors['lines'] = ['少なくとも 1 行、勘定科目を選択してください。'];
        }

        if ($errors === []) {
            try {
                $out = $this->createBudget->execute(new CreateBudgetInput(
                    entityId: $entityId,
                    fiscalTermId: $fiscalTermId,
                    name: $name,
                    notes: $notes === '' ? null : $notes,
                    lineItems: $lineInputs,
                    createdBy: $userId,
                ));
                $this->flash->addSuccess('予算を作成しました（Draft）。');
                return HtmlResponse::redirect('/ui/budgets/' . $out->budget->id);
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (\Throwable $e) {
                $errors['_'] = ['登録に失敗しました: ' . $e->getMessage()];
            }
        }

        return $this->renderForm(
            entityId: $entityId,
            name: $name,
            notes: $notes,
            fiscalTermId: $fiscalTermId,
            lines: $formLines,
            errors: $errors,
            status: 422,
        );
    }

    private function guard(): ?HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        if ($this->session->getSelectedEntity() === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }
        return null;
    }

    /**
     * @param list<array{account_title_id: string, monthly: list<string>, memo: string}> $lines
     * @param array<string, list<string>> $errors
     */
    private function renderForm(
        string $entityId,
        string $name,
        string $notes,
        string $fiscalTermId,
        array $lines,
        array $errors,
        int $status,
    ): HtmlResponse {
        $data = [
            'page_title'           => '新規予算',
            'active_nav'           => 'budgets',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'csrf_form_token'      => $this->csrf->generateToken(self::CSRF_FORM_ID),
            'csrf_form_field'      => self::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'entities'             => [],
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'       => $this->flash->consume(),
            'form_mode'            => 'new',
            'form_action'          => '/ui/budgets/new',
            'form_name'            => $name,
            'form_notes'           => $notes,
            'form_fiscal_term_id'  => $fiscalTermId,
            'form_lines'           => $lines,
            'form_errors'          => $errors,
            'account_titles'       => $this->ctx->accountTitlesForEntity($entityId),
            'fiscal_terms'         => $this->ctx->fiscalTermsForEntity($entityId),
        ];
        return HtmlResponse::of($status, $this->view->render('budgets/form.html.tpl', $data));
    }

    /**
     * @return array{account_title_id: string, monthly: list<string>, memo: string}
     */
    private static function blankLine(): array
    {
        /** @var list<string> $monthly */
        $monthly = array_fill(0, 12, '');
        return [
            'account_title_id' => '',
            'monthly'          => $monthly,
            'memo'             => '',
        ];
    }
}
