<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\CreateConsumptionTaxPeriodUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\SimplifiedBusinessCategory;
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
 * GET  /ui/consumption-tax/periods/new — new consumption-tax period form.
 * POST /ui/consumption-tax/periods/new — create a period row.
 */
final readonly class ConsumptionTaxPeriodNewController
{
    public const CSRF_FORM_ID = 'ui_consumption_tax_period_new';

    public function __construct(
        private CreateConsumptionTaxPeriodUseCase $createPeriod,
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
            form: self::blankForm($default),
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

        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/consumption-tax/periods/new');
        }

        $form = [
            'fiscalTermId'             => PlanningFormSupport::str($body, 'fiscal_term_id'),
            'periodFrom'               => PlanningFormSupport::str($body, 'period_from'),
            'periodTo'                 => PlanningFormSupport::str($body, 'period_to'),
            'method'                   => PlanningFormSupport::str($body, 'method', 'principle'),
            'simplifiedBusinessCategory' => PlanningFormSupport::str($body, 'simplified_business_category'),
            'isInterim'                => PlanningFormSupport::bool($body['is_interim'] ?? null) ? '1' : '',
        ];

        $errors = [];
        if ($form['fiscalTermId'] === '') {
            $errors['fiscal_term_id'] = ['会計期間を選択してください。'];
        }
        if ($form['periodFrom'] === '' || $form['periodTo'] === '') {
            $errors['period_from'] = ['期間の開始日と終了日を入力してください。'];
        }

        if ($errors === []) {
            try {
                $this->createPeriod->execute(
                    entityId: $entityId,
                    fiscalTermId: $form['fiscalTermId'],
                    periodFromIso: $form['periodFrom'],
                    periodToIso: $form['periodTo'],
                    method: $form['method'],
                    simplifiedBusinessCategory: $form['simplifiedBusinessCategory'] === ''
                        ? null
                        : (int) $form['simplifiedBusinessCategory'],
                    isInterim: $form['isInterim'] === '1',
                );
                $this->flash->addSuccess('消費税申告期間を登録しました。');
                return HtmlResponse::redirect('/ui/consumption-tax/periods');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (\Throwable $e) {
                $errors['_'] = ['登録に失敗しました: ' . $e->getMessage()];
            }
        }

        return $this->renderForm(entityId: $entityId, form: $form, errors: $errors, status: 422);
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
     * @param array<string, string> $form
     * @param array<string, list<string>> $errors
     */
    private function renderForm(string $entityId, array $form, array $errors, int $status): HtmlResponse
    {
        $data = [
            'page_title'           => '新規消費税申告期間',
            'active_nav'           => 'consumption_tax',
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
            'form_action'          => '/ui/consumption-tax/periods/new',
            'form'                 => $form,
            'form_errors'          => $errors,
            'fiscal_terms'         => $this->ctx->fiscalTermsForEntity($entityId),
            'method_options'       => self::methodOptions(),
            'category_options'     => self::categoryOptions(),
        ];
        return HtmlResponse::of($status, $this->view->render('consumption_tax/period_form.html.tpl', $data));
    }

    /**
     * @return array<string, string>
     */
    private static function blankForm(?string $defaultTermId): array
    {
        return [
            'fiscalTermId'               => $defaultTermId ?? '',
            'periodFrom'                 => '',
            'periodTo'                   => '',
            'method'                     => 'principle',
            'simplifiedBusinessCategory' => '',
            'isInterim'                  => '',
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function methodOptions(): array
    {
        return array_map(
            static fn (ConsumptionTaxCalculationMethod $m): array => [
                'value' => $m->value,
                'label' => $m->label(),
            ],
            ConsumptionTaxCalculationMethod::cases(),
        );
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function categoryOptions(): array
    {
        return array_map(
            static fn (SimplifiedBusinessCategory $c): array => [
                'value' => (string) $c->value,
                'label' => $c->label(),
            ],
            SimplifiedBusinessCategory::cases(),
        );
    }
}
