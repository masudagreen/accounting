<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui;

use DateTimeZone;
use Rucaro\Domain\Auth\ApiTokenRepositoryInterface;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/logout — revokes the API token behind the session, then destroys
 * the session and redirects back to the login page.
 *
 * A GET request is not accepted: logout is state-changing and must be guarded
 * by CSRF, so we only honour POST with a valid token.
 */
final readonly class LogoutController
{
    public const CSRF_FORM_ID = 'ui_logout';

    public function __construct(
        private ApiTokenRepositoryInterface $tokens,
        private ClockInterface $clock,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
    ) {
    }

    public function __invoke(ServerRequest $request): HtmlResponse
    {
        if ($request->method !== 'POST') {
            return HtmlResponse::redirect('/ui/login');
        }

        $submitted = $this->extractCsrf($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('ログアウトに失敗しました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $plaintext = $this->session->getTokenPlaintext();
        if ($plaintext !== null) {
            $hash = BearerTokenGenerator::hash($plaintext);
            $record = $this->tokens->findByHash($hash);
            if ($record !== null) {
                $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
                $this->tokens->revoke($record->id, $now);
            }
        }

        $this->session->destroy();
        return HtmlResponse::redirect('/ui/login');
    }

    private function extractCsrf(ServerRequest $request): string
    {
        $parsed = [];
        parse_str($request->rawBody, $parsed);
        $v = $parsed['_csrf'] ?? '';
        return is_string($v) ? $v : '';
    }
}
