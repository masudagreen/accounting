<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BlueReturn;

use Rucaro\Application\BlueReturn\FinalizeBlueReturnUseCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BlueReturn\BlueReturnJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/blue-returns/{id}/finalize */
final readonly class FinalizeBlueReturnController
{
    public function __construct(
        private FinalizeBlueReturnUseCase $useCase,
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
            $out = $this->useCase->execute($id);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(409, 'INVARIANT_VIOLATION', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(BlueReturnJsonSerializer::toArray($out->form));
    }
}
