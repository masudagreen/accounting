<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Auth;

use DateTimeZone;
use Rucaro\Application\Auth\GetMyProfileUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class MeController
{
    public function __construct(
        private GetMyProfileUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }
        try {
            $user = $this->useCase->execute($userId);
        } catch (EntityNotFoundException) {
            return ErrorResponse::unauthorized('User no longer exists.');
        }

        return EnvelopeResponse::ok(data: [
            'id'          => $user->id,
            'loginId'     => $user->loginId,
            'displayName' => $user->displayName,
            'email'       => $user->email,
            'isActive'    => $user->isActive,
            'lastLoginAt' => $user->lastLoginAt?->setTimezone(new DateTimeZone('UTC'))?->format('Y-m-d\TH:i:s.u\Z'),
            'createdAt'   => $user->createdAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'updatedAt'   => $user->updatedAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
        ]);
    }
}
