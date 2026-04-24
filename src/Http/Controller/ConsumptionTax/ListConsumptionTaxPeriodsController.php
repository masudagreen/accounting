<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\ListConsumptionTaxPeriodsUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/consumption-tax/periods?entityId= */
final readonly class ListConsumptionTaxPeriodsController
{
    public function __construct(
        private ListConsumptionTaxPeriodsUseCase $useCase,
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
        $periods = $this->useCase->execute($entityId);
        return EnvelopeResponse::list(
            ConsumptionTaxSettlementJsonSerializer::periodsToArrayList($periods),
            ['total' => count($periods)],
        );
    }
}
