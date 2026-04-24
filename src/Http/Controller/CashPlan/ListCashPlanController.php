<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\CashPlan;

use Rucaro\Application\CashPlan\ListCashPlansUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\CashPlan\CashPlanJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/cash-plans?entityId=&fiscalTermId= */
final readonly class ListCashPlanController
{
    public function __construct(
        private ListCashPlansUseCase $useCase,
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
            return ErrorResponse::badRequest('entityId query parameter is required and must be a ULID.');
        }
        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($fiscalTermId !== null && !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a ULID when provided.');
        }

        $plans = $this->useCase->execute($entityId, $fiscalTermId);
        return EnvelopeResponse::list(
            CashPlanJsonSerializer::toArrayList($plans),
            ['total' => count($plans)],
        );
    }
}
