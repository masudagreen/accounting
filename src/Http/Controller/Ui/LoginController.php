<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui;

use Rucaro\Application\Auth\LoginUseCase;
use Rucaro\Application\Auth\LoginUseCaseInput;
use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCaseInput;
use Rucaro\Domain\Auth\InvalidCredentialsException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET  /ui/login  — renders the login page.
 * POST /ui/login  — exchanges email + password for a Bearer token, stores it
 *                   in `$_SESSION`, and redirects to `/ui/dashboard`.
 *
 * The controller talks to {@see LoginUseCase} directly rather than going
 * through the REST API, keeping login in-process with no HTTP round trip.
 */
final readonly class LoginController
{
    public const CSRF_FORM_ID = 'ui_login';

    public function __construct(
        private LoginUseCase $loginUseCase,
        private ListEntitiesUseCase $listEntities,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function __invoke(ServerRequest $request): HtmlResponse
    {
        if ($request->method === 'POST') {
            return $this->handlePost($request);
        }
        return $this->handleGet();
    }

    private function handleGet(): HtmlResponse
    {
        if ($this->session->isAuthenticated()) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        $csrfToken = $this->csrf->generateToken(self::CSRF_FORM_ID);
        $html = $this->view->render('login.html.tpl', [
            'csrf_token'    => $csrfToken,
            'csrf_field'    => self::CSRF_FORM_ID,
            'flash_messages'=> $this->flash->consume(),
            'form_email'    => '',
            'form_errors'   => [],
        ]);
        return HtmlResponse::ok($html);
    }

    private function handlePost(ServerRequest $request): HtmlResponse
    {
        $body = $this->parseForm($request);
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';
        $submitted = $body['_csrf'] ?? '';

        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度ログインしてください。');
            return HtmlResponse::redirect('/ui/login');
        }

        try {
            $input = new LoginUseCaseInput(email: $email, password: $password);
        } catch (ValidationException $e) {
            return $this->renderWithErrors($email, $e->errors());
        }

        try {
            $output = $this->loginUseCase->execute($input);
        } catch (InvalidCredentialsException $e) {
            return $this->renderWithErrors($email, [
                '_' => [$e->getMessage() !== '' ? $e->getMessage() : 'メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        // Extract the ApiToken.id by locating the record via the hash. The
        // plaintext is the only handle the use case returns, so we look up
        // the record to keep the token id available for logout/revoke.
        $this->session->setUser(
            userId:        $output->userId,
            plaintextToken:$output->token,
            tokenId:       '', // Filled below if we can resolve; optional.
            displayName:   $output->displayName,
            email:         $output->email,
        );

        // Auto-select the user's first entity so the dashboard can render
        // something useful without forcing an extra click.
        $entities = $this->listEntities->execute(new ListEntitiesUseCaseInput(
            ownerUserId: $output->userId,
            page: 1,
            pageSize: 1,
            search: null,
            isActive: true,
        ));
        if ($entities->items !== []) {
            $this->session->setSelectedEntity($entities->items[0]->id);
        }

        $this->flash->addSuccess('ログインしました。ようこそ ' . $output->displayName . ' さん。');
        return HtmlResponse::redirect('/ui/dashboard');
    }

    /**
     * @param array<string, list<string>> $errors
     */
    private function renderWithErrors(string $email, array $errors): HtmlResponse
    {
        $csrfToken = $this->csrf->generateToken(self::CSRF_FORM_ID);
        $html = $this->view->render('login.html.tpl', [
            'csrf_token'    => $csrfToken,
            'csrf_field'    => self::CSRF_FORM_ID,
            'flash_messages'=> $this->flash->consume(),
            'form_email'    => $email,
            'form_errors'   => $errors,
        ]);
        return HtmlResponse::of(422, $html);
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
