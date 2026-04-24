<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BreakEvenPoint;

use Rucaro\Application\BreakEvenPoint\ListCvpClassificationsUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BreakEvenPoint\BreakEvenPointJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/cvp-classifications?entityId= */
final readonly class ListCvpClassificationController
{
    public function __construct(
        private ListCvpClassificationsUseCase $useCase,
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
        $rows = $this->useCase->execute($entityId);
        return EnvelopeResponse::list(
            BreakEvenPointJsonSerializer::classificationsToArray($rows),
            ['total' => count($rows)],
        );
    }
}
