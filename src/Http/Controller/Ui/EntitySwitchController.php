<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui;

use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/entity/switch — updates the selected entity / fiscal term in the
 * session and redirects back to the Referer (or /ui/dashboard if none).
 *
 * Selection is UI state only; the downstream use cases still receive the id
 * as an explicit argument, so this controller never leaks authority.
 */
final readonly class EntitySwitchController
{
    public const CSRF_FORM_ID = 'ui_entity_switch';

    public function __construct(
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
    ) {
    }

    public function __invoke(ServerRequest $request): HtmlResponse
    {
        if ($request->method !== 'POST') {
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $body = $this->parseForm($request);
        $submitted = $body['_csrf'] ?? '';
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('不正なリクエストを検出しました。もう一度お試しください。');
            return HtmlResponse::redirect($this->resolveReturn($request));
        }

        $entityId = $body['entity_id'] ?? '';
        $fiscalTermId = $body['fiscal_term_id'] ?? '';

        if ($entityId !== '') {
            $this->session->setSelectedEntity($entityId);
        }
        if ($fiscalTermId !== '') {
            $this->session->setSelectedFiscalTerm($fiscalTermId);
        }

        $this->flash->addInfo('対象を切り替えました。');
        return HtmlResponse::redirect($this->resolveReturn($request));
    }

    private function resolveReturn(ServerRequest $request): string
    {
        $referer = $request->header('referer');
        if ($referer !== null && str_starts_with($referer, '/ui/')) {
            return $referer;
        }
        // Some clients send the full URL; accept it but strip the origin.
        if ($referer !== null && preg_match('#https?://[^/]+(/ui/[^\s]*)#', $referer, $m) === 1) {
            return $m[1];
        }
        return '/ui/dashboard';
    }

    /**
     * @return array<string, string>
     */
    private function parseForm(ServerRequest $request): array
    {
        $parsed = [];
        parse_str($request->rawBody, $parsed);
        /** @var array<string, string> $out */
        $out = [];
        foreach ($parsed as $k => $v) {
            if (is_string($k) && is_string($v)) {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
