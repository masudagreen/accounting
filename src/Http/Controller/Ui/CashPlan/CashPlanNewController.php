<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\CashPlan;

use Rucaro\Application\CashPlan\CashPlanEntryInput;
use Rucaro\Application\CashPlan\CreateCashPlanInput;
use Rucaro\Application\CashPlan\CreateCashPlanUseCase;
use Rucaro\Domain\CashPlan\CashPlanCategory;
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
 * GET  /ui/cash-plans/new — cash-plan form with 6 category × 12 month grid.
 * POST /ui/cash-plans/new — create a new 資金繰り計画.
 */
final readonly class CashPlanNewController
{
    public const CSRF_FORM_ID = 'ui_cash_plan_new';

    public function __construct(
        private CreateCashPlanUseCase $createPlan,
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
            openingBalance: '0',
            currency: 'JPY',
            notes: '',
            fiscalTermId: $default ?? ($this->session->getSelectedFiscalTerm() ?? ''),
            entries: [self::blankEntry()],
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
            return HtmlResponse::redirect('/ui/cash-plans/new');
        }

        $name         = PlanningFormSupport::str($body, 'name');
        $opening      = PlanningFormSupport::normalizeAmount(PlanningFormSupport::str($body, 'opening_balance', '0'));
        $currency     = strtoupper(PlanningFormSupport::str($body, 'currency_code', 'JPY'));
        $notes        = PlanningFormSupport::str($body, 'notes');
        $fiscalTermId = PlanningFormSupport::str($body, 'fiscal_term_id');

        $rows = PlanningFormSupport::extractMonthlyRows($body, 'entries');
        $formEntries = [];
        $entryInputs = [];
        $sortOrder = 0;
        foreach ($rows as $row) {
            $formEntries[] = [
                'label'    => $row['label'],
                'category' => $row['category'],
                'monthly'  => $row['monthly'],
                'memo'     => $row['memo'],
            ];
            if ($row['label'] !== '' && $row['category'] !== '') {
                $entryInputs[] = new CashPlanEntryInput(
                    category: $row['category'],
                    label: $row['label'],
                    sortOrder: $sortOrder++,
                    monthlyAmounts: $row['monthly'],
                    memo: $row['memo'] === '' ? null : $row['memo'],
                );
            }
        }
        if ($formEntries === []) {
            $formEntries[] = self::blankEntry();
        }

        $errors = [];
        if ($name === '') {
            $errors['name'] = ['計画名を入力してください。'];
        }
        if ($fiscalTermId === '') {
            $errors['fiscal_term_id'] = ['会計期間を選択してください。'];
        }
        if ($entryInputs === []) {
            $errors['entries'] = ['少なくとも 1 行、ラベルと区分を入力してください。'];
        }

        if ($errors === []) {
            try {
                $out = $this->createPlan->execute(new CreateCashPlanInput(
                    entityId: $entityId,
                    fiscalTermId: $fiscalTermId,
                    name: $name,
                    openingBalance: $opening,
                    currencyCode: $currency,
                    notes: $notes === '' ? null : $notes,
                    entries: $entryInputs,
                    createdBy: $userId,
                ));
                $this->flash->addSuccess('資金繰り計画を作成しました。');
                return HtmlResponse::redirect('/ui/cash-plans/' . $out->plan->id);
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (\Throwable $e) {
                $errors['_'] = ['登録に失敗しました: ' . $e->getMessage()];
            }
        }

        return $this->renderForm(
            entityId: $entityId,
            name: $name,
            openingBalance: $opening,
            currency: $currency,
            notes: $notes,
            fiscalTermId: $fiscalTermId,
            entries: $formEntries,
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
     * @param list<array{label: string, category: string, monthly: list<string>, memo: string}> $entries
     * @param array<string, list<string>> $errors
     */
    private function renderForm(
        string $entityId,
        string $name,
        string $openingBalance,
        string $currency,
        string $notes,
        string $fiscalTermId,
        array $entries,
        array $errors,
        int $status,
    ): HtmlResponse {
        $data = [
            'page_title'           => '新規資金繰り計画',
            'active_nav'           => 'cash_plans',
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
            'form_action'          => '/ui/cash-plans/new',
            'form_name'            => $name,
            'form_opening_balance' => $openingBalance,
            'form_currency'        => $currency,
            'form_notes'           => $notes,
            'form_fiscal_term_id'  => $fiscalTermId,
            'form_entries'         => $entries,
            'form_errors'          => $errors,
            'fiscal_terms'         => $this->ctx->fiscalTermsForEntity($entityId),
            'category_options'     => self::categoryOptions(),
        ];
        return HtmlResponse::of($status, $this->view->render('cash_plans/form.html.tpl', $data));
    }

    /**
     * @return array{label: string, category: string, monthly: list<string>, memo: string}
     */
    private static function blankEntry(): array
    {
        /** @var list<string> $monthly */
        $monthly = array_fill(0, 12, '');
        return [
            'label'    => '',
            'category' => 'operating_in',
            'monthly'  => $monthly,
            'memo'     => '',
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function categoryOptions(): array
    {
        return [
            ['value' => CashPlanCategory::OperatingIn->value,  'label' => '営業収入'],
            ['value' => CashPlanCategory::OperatingOut->value, 'label' => '営業支出'],
            ['value' => CashPlanCategory::InvestingIn->value,  'label' => '投資収入'],
            ['value' => CashPlanCategory::InvestingOut->value, 'label' => '投資支出'],
            ['value' => CashPlanCategory::FinancingIn->value,  'label' => '財務収入'],
            ['value' => CashPlanCategory::FinancingOut->value, 'label' => '財務支出'],
        ];
    }
}
