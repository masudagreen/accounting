<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleInput;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleUseCase;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/fixed-assets/{id}/depreciate — generate (or refresh) the
 * depreciation schedule for one fiscal term.
 */
final readonly class FixedAssetDepreciateController
{
    public const CSRF_FORM_ID = 'ui_fixed_asset_depreciate';

    public function __construct(
        private GenerateDepreciationScheduleUseCase $generate,
        private PlanningUiContext $ctx,
        private ClockInterface $clock,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
    ) {
    }

    public function submit(ServerRequest $request, string $id = ''): HtmlResponse
    {
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
        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
        }

        $fiscalTermId = PlanningFormSupport::str($body, 'fiscal_term_id');
        $fiscalTerms  = $this->ctx->fiscalTermsForEntity($entityId);
        if ($fiscalTermId === '') {
            $fiscalTermId = PlanningUiContext::defaultFiscalTermId($fiscalTerms, $this->clock->getCurrentTime()) ?? '';
        }
        if ($fiscalTermId === '') {
            $this->flash->addError('会計期間が登録されていません。');
            return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
        }
        $term = $this->ctx->findFiscalTerm($fiscalTermId);
        if ($term === null || $term['startDate'] === '' || $term['endDate'] === '') {
            $this->flash->addError('会計期間の日付が未設定です。');
            return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
        }
        try {
            $start = new DateTimeImmutable($term['startDate'], new DateTimeZone('UTC'));
            $end   = new DateTimeImmutable($term['endDate'], new DateTimeZone('UTC'));
            $this->generate->execute(new GenerateDepreciationScheduleInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                fiscalTermStart: $start,
                fiscalTermEnd: $end,
                fixedAssetId: $id,
            ));
            $this->flash->addSuccess('減価償却スケジュールを生成しました。');
        } catch (\Throwable $e) {
            $this->flash->addError('償却スケジュール生成に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
    }
}
