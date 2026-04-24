<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BlueReturn;

use Rucaro\Application\BlueReturn\DeleteBlueReturnUseCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** DELETE /api/v1/blue-returns/{id} */
final readonly class DeleteBlueReturnController
{
    public function __construct(
        private DeleteBlueReturnUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $id = $request->queryString('id');
        if ($id === null || !UlidGenerator::isValid($id)) {
            return ErrorResponse::badRequest('id must be a ULID.');
        }
        try {
            $this->useCase->execute($id);
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(409, 'INVARIANT_VIOLATION', $e->getMessage());
        }
        return EnvelopeResponse::ok(['id' => $id]);
    }
}
