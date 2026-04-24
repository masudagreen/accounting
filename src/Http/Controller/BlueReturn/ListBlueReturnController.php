<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BlueReturn;

use Rucaro\Application\BlueReturn\ListBlueReturnsUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BlueReturn\BlueReturnJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/blue-returns?entityId=&fiscalTermId= */
final readonly class ListBlueReturnController
{
    public function __construct(
        private ListBlueReturnsUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $entityId = $request->queryString('entityId');
        if ($entityId === null || !UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($fiscalTermId !== null && !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a ULID when provided.');
        }
        $forms = $this->useCase->execute($entityId, $fiscalTermId);
        return EnvelopeResponse::ok(BlueReturnJsonSerializer::toArrayList($forms));
    }
}
