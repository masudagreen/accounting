<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\CashPlan;

use Rucaro\Application\CashPlan\GetCashPlanUseCase;
use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/cash-plans/{id} — render a cash plan including its monthly
 * running balance.
 */
final readonly class CashPlanShowController
{
    public const CSRF_FORM_ID = 'ui_cash_plan_show';

    public function __construct(
        private GetCashPlanUseCase $getPlan,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function invoke(ServerRequest $request, string $id = ''): HtmlResponse
    {
        unset($request);
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        $plan = $this->getPlan->execute($id);
        if ($plan === null || $plan->entityId !== $entityId) {
            return HtmlResponse::notFound('資金繰り計画が見つかりません。');
        }

        $data = [
            'page_title'           => '資金繰り計画詳細',
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
            'plan'                 => self::planToArray($plan),
            'monthly_deltas'       => self::monthlyDeltas($plan),
            'running_balances'     => self::runningBalances($plan),
        ];
        return HtmlResponse::ok($this->view->render('cash_plans/show.html.tpl', $data));
    }

    /**
     * @return array<string, mixed>
     */
    private static function planToArray(CashPlan $p): array
    {
        return [
            'id'             => $p->id,
            'name'           => $p->name,
            'openingBalance' => $p->openingBalance,
            'currency'       => $p->currencyCode,
            'notes'          => $p->notes ?? '',
            'updatedAt'      => $p->updatedAt->format('Y-m-d H:i'),
            'entries'        => array_map(
                static fn (CashPlanEntry $e): array => [
                    'id'       => $e->id,
                    'category' => $e->category->value,
                    'label'    => $e->label,
                    'memo'     => $e->memo ?? '',
                    'monthly'  => $e->monthlyAmounts,
                    'total'    => $e->total(),
                ],
                $p->entries,
            ),
        ];
    }

    /**
     * @return list<string>
     */
    private static function monthlyDeltas(CashPlan $p): array
    {
        $out = [];
        for ($m = 1; $m <= 12; $m++) {
            $out[] = $p->monthlyDelta($m);
        }
        return $out;
    }

    /**
     * @return list<string>
     */
    private static function runningBalances(CashPlan $p): array
    {
        $out = [];
        for ($m = 1; $m <= 12; $m++) {
            $out[] = $p->closingBalance($m);
        }
        return $out;
    }
}
