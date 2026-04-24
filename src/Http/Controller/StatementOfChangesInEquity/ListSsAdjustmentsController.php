<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\ListSsAdjustmentsUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\StatementOfChangesInEquity\StatementOfChangesInEquityJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/ss-adjustments?entityId=&fiscalTermId= */
final readonly class ListSsAdjustmentsController
{
    public function __construct(
        private ListSsAdjustmentsUseCase $useCase,
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
        if ($fiscalTermId === null || !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId query parameter is required and must be a ULID.');
        }
        try {
            $rows = $this->useCase->execute($entityId, $fiscalTermId);
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::list(
            StatementOfChangesInEquityJsonSerializer::adjustmentListToArray($rows),
            ['total' => count($rows)],
        );
    }
}
