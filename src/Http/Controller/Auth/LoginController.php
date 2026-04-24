<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Auth;

use Rucaro\Application\Auth\LoginUseCase;
use Rucaro\Application\Auth\LoginUseCaseInput;
use Rucaro\Domain\Auth\InvalidCredentialsException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class LoginController
{
    public function __construct(
        private LoginUseCase $useCase,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $body = $request->json;
        if (!is_array($body)) {
            return ErrorResponse::badRequest('Request body must be a JSON object.');
        }

        $email = isset($body['email']) && is_string($body['email']) ? $body['email'] : '';
        $password = isset($body['password']) && is_string($body['password']) ? $body['password'] : '';

        try {
            $input = new LoginUseCaseInput(email: $email, password: $password);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable(
                'Login payload failed validation.',
                $e->errors(),
            );
        }

        try {
            $output = $this->useCase->execute($input);
        } catch (InvalidCredentialsException $e) {
            return ErrorResponse::of(401, 'INVALID_CREDENTIALS', $e->getMessage());
        }

        return EnvelopeResponse::ok(data: [
            'token'       => $output->token,
            'tokenPrefix' => $output->tokenPrefix,
            'issuedAt'    => $output->issuedAt->format('Y-m-d\TH:i:s.u\Z'),
            'expiresAt'   => $output->expiresAt->format('Y-m-d\TH:i:s.u\Z'),
            'user'        => [
                'id'          => $output->userId,
                'loginId'     => $output->loginId,
                'displayName' => $output->displayName,
                'email'       => $output->email,
            ],
        ]);
    }
}
