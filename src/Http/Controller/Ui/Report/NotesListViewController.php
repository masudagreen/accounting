<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use Rucaro\Application\FinancialStatementNotes\ListFsNotesUseCase;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNotesPdfGeneratorInterface;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/notes — 注記表 read-only listing, grouped by category.
 */
final readonly class NotesListViewController
{
    public function __construct(
        private ListFsNotesUseCase $listNotes,
        private FsNotesPdfGeneratorInterface $pdfGenerator,
        private PeriodQueryHelper $period,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function __invoke(ServerRequest $request): HtmlResponse
    {
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addError('会計単位 (entity) が未選択です。上部ナビから選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($fiscalTermId === null || $fiscalTermId === '') {
            $fiscalTermId = $this->session->getSelectedFiscalTerm()
                ?? $this->period->findLatestFiscalTermId($entityId);
        }
        if ($fiscalTermId === null) {
            $this->flash->addError('会計期 (fiscal_term) が登録されていません。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $notes = $this->listNotes->execute($entityId, $fiscalTermId, false);

        $format = strtolower($request->queryString('format') ?? 'html');
        if ($format === 'pdf') {
            $pdf = $this->pdfGenerator->render($notes, $entityId, $fiscalTermId);
            return new HtmlResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="fs-notes.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }

        $data = [
            'page_title'           => '注記表',
            'active_nav'           => 'notes',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $fiscalTermId,
            'entities'             => [],
            'total_count'          => count($notes),
            'categories'           => self::groupByCategory($notes),
            'flash_messages'       => $this->flash->consume(),
        ];
        return HtmlResponse::ok($this->view->render('notes/list.html.tpl', $data));
    }

    /**
     * @param list<FinancialStatementNote> $notes
     * @return list<array{code: string, label: string, order: int, notes: list<array{id: string, label: string, body: string, templateCode: string, sortOrder: int, isActive: bool}>}>
     */
    private static function groupByCategory(array $notes): array
    {
        /** @var array<string, list<FinancialStatementNote>> $grouped */
        $grouped = [];
        foreach ($notes as $note) {
            $grouped[$note->category->value][] = $note;
        }

        $out = [];
        foreach (FsNoteCategory::cases() as $cat) {
            $bucket = $grouped[$cat->value] ?? [];
            // sort within category by sortOrder ascending
            usort(
                $bucket,
                static fn (FinancialStatementNote $a, FinancialStatementNote $b): int
                    => $a->sortOrder <=> $b->sortOrder,
            );
            $out[] = [
                'code'  => $cat->value,
                'label' => $cat->jaLabel(),
                'order' => $cat->displayOrder(),
                'notes' => array_map(self::noteToArray(...), $bucket),
            ];
        }
        // Drop categories with no notes so the rendered list stays tidy.
        $out = array_values(array_filter(
            $out,
            static fn (array $group): bool => $group['notes'] !== [],
        ));
        usort($out, static fn (array $a, array $b): int => $a['order'] <=> $b['order']);
        return $out;
    }

    /**
     * @return array{id: string, label: string, body: string, templateCode: string, sortOrder: int, isActive: bool}
     */
    private static function noteToArray(FinancialStatementNote $n): array
    {
        return [
            'id'           => $n->id,
            'label'        => $n->label,
            'body'         => $n->body,
            'templateCode' => $n->templateCode ?? '',
            'sortOrder'    => $n->sortOrder,
            'isActive'     => $n->isActive,
        ];
    }
}
