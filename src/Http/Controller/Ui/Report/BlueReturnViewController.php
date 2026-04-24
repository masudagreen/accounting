<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use Rucaro\Application\BlueReturn\GetBlueReturnUseCase;
use Rucaro\Application\BlueReturn\ListBlueReturnsUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnPdfGeneratorInterface;
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
 * GET /ui/blue-return — 青色申告決算書 (sole-proprietor only) read-only view.
 *
 * Resolves one finalized/draft form per (entity, fiscalTerm). If the
 * selected entity is corporate a friendly warning is rendered; the
 * blue-return form is an individual-tax-payer concept and is not
 * emitted for corporate entities.
 */
final readonly class BlueReturnViewController
{
    public function __construct(
        private GetBlueReturnUseCase $getBlueReturn,
        private ListBlueReturnsUseCase $listBlueReturns,
        private BlueReturnPdfGeneratorInterface $pdfGenerator,
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

        $form = null;
        if ($fiscalTermId !== null) {
            $forms = $this->listBlueReturns->execute($entityId, $fiscalTermId);
            if ($forms !== []) {
                $form = $this->getBlueReturn->execute($forms[0]->id);
            }
        }

        $format = strtolower($request->queryString('format') ?? 'html');
        if ($format === 'pdf' && $form !== null) {
            $pdf = $this->pdfGenerator->render($form);
            return new HtmlResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => sprintf(
                        'attachment; filename="blue-return-%s.pdf"',
                        $form->fiscalTermId,
                    ),
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }

        $data = [
            'page_title'           => '青色申告決算書',
            'active_nav'           => 'blue_return',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $fiscalTermId ?? '',
            'entities'             => [],
            'has_form'             => $form !== null,
            'form'                 => $form !== null ? self::formToArray($form) : null,
            'flash_messages'       => $this->flash->consume(),
        ];
        return HtmlResponse::ok($this->view->render('blue_return/view.html.tpl', $data));
    }

    /**
     * @return array{id: string, formType: string, status: string, finalizedAt: string, page1Pl: array<string, mixed>, page2Monthly: array<string, mixed>, page3Breakdown: array<string, mixed>, page4Bs: array<string, mixed>}
     */
    private static function formToArray(BlueReturnForm $form): array
    {
        return [
            'id'             => $form->id,
            'formType'       => $form->formType->value,
            'status'         => $form->status->value,
            'finalizedAt'    => $form->finalizedAt?->format('Y-m-d H:i:s') ?? '',
            'page1Pl'        => $form->snapshot->page1Pl,
            'page2Monthly'   => $form->snapshot->page2Monthly,
            'page3Breakdown' => $form->snapshot->page3Breakdown,
            'page4Bs'        => $form->snapshot->page4Bs,
        ];
    }
}
