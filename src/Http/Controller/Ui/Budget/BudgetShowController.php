<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Budget;

use Rucaro\Application\Budget\GetBudgetUseCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
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
 * GET /ui/budgets/{id} — render a budget header, its 12×N monthly grid and
 * lifecycle action buttons (approve / lock / delete).
 */
final readonly class BudgetShowController
{
    public const CSRF_FORM_ID = 'ui_budget_show';

    public function __construct(
        private GetBudgetUseCase $getBudget,
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
        $budget = $this->getBudget->execute($id);
        if ($budget === null || $budget->entityId !== $entityId) {
            return HtmlResponse::notFound('予算が見つかりません。');
        }

        $data = [
            'page_title'           => '予算詳細',
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
            'budget'               => self::budgetToArray($budget),
            'monthly_totals'       => self::monthlyTotals($budget),
            'can_edit'             => $budget->status->isEditable(),
            'can_approve'          => $budget->status->isEditable(),
            'can_lock'             => $budget->status->isApproved() && !$budget->status->isLocked(),
        ];
        return HtmlResponse::ok($this->view->render('budgets/show.html.tpl', $data));
    }

    /**
     * @return array<string, mixed>
     */
    private static function budgetToArray(Budget $b): array
    {
        return [
            'id'           => $b->id,
            'name'         => $b->name,
            'status'       => $b->status->value,
            'fiscalTermId' => $b->fiscalTermId,
            'notes'        => $b->notes ?? '',
            'annualTotal'  => $b->annualTotal(),
            'lines'        => array_map(
                static fn (BudgetLineItem $li): array => [
                    'id'             => $li->id,
                    'accountTitleId' => $li->accountTitleId,
                    'memo'           => $li->memo ?? '',
                    'monthly'        => $li->monthlyAmounts,
                    'total'          => $li->totalAmount(),
                ],
                $b->lineItems,
            ),
            'approvedBy'   => $b->approvedBy ?? '',
            'approvedAt'   => $b->approvedAt?->format('Y-m-d H:i') ?? '',
            'updatedAt'    => $b->updatedAt->format('Y-m-d H:i'),
        ];
    }

    /**
     * @return list<string>
     */
    private static function monthlyTotals(Budget $b): array
    {
        $out = [];
        for ($m = 1; $m <= 12; $m++) {
            $out[] = $b->monthlyTotal($m);
        }
        return $out;
    }
}
