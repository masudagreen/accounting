<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui;

use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCaseInput;
use Rucaro\Application\Journal\ListJournalsUseCase;
use Rucaro\Application\Journal\ListJournalsUseCaseInput;
use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/dashboard — the first page the operator sees after login.
 *
 * Pulls:
 *   - The owner's entities (for the navbar selector)
 *   - Latest 5 journals for the currently-selected entity, if any
 *
 * Phase 7-2/7-3 will expand this with more widgets, but the layout blocks
 * are already in place so those phases only need to drop in new cards.
 */
final readonly class DashboardController
{
    public function __construct(
        private ListEntitiesUseCase $listEntities,
        private ListJournalsUseCase $listJournals,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function __invoke(ServerRequest $request): HtmlResponse
    {
        unset($request);
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return HtmlResponse::redirect('/ui/login');
        }

        $entitiesOut = $this->listEntities->execute(new ListEntitiesUseCaseInput(
            ownerUserId: $userId,
            page: 1,
            pageSize: 50,
            search: null,
            isActive: true,
        ));

        $selectedEntityId = $this->session->getSelectedEntity();
        if ($selectedEntityId === null && $entitiesOut->items !== []) {
            $selectedEntityId = $entitiesOut->items[0]->id;
            $this->session->setSelectedEntity($selectedEntityId);
        }

        $recentJournals = [];
        if ($selectedEntityId !== null) {
            try {
                $journalsOut = $this->listJournals->execute(new ListJournalsUseCaseInput(
                    entityId: $selectedEntityId,
                    page: 1,
                    pageSize: 5,
                ));
                $recentJournals = array_map(
                    static fn (Journal $j): array => [
                        'id'          => $j->id,
                        'journalDate' => $j->journalDate->format('Y-m-d'),
                        'summary'     => $j->summary,
                        'totalAmount' => $j->totalAmount,
                        'status'      => $j->status,
                    ],
                    $journalsOut->items,
                );
            } catch (\Throwable) {
                $recentJournals = [];
            }
        }

        $data = [
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'entities'             => array_map(self::entityToArray(...), $entitiesOut->items),
            'selected_entity_id'   => $selectedEntityId ?? '',
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'recent_journals'      => $recentJournals,
            'flash_messages'       => $this->flash->consume(),
        ];

        return HtmlResponse::ok($this->view->render('dashboard.html.tpl', $data));
    }

    /**
     * @return array{id: string, name: string}
     */
    private static function entityToArray(Entity $e): array
    {
        return ['id' => $e->id, 'name' => $e->name];
    }
}
