<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Budget;

use Rucaro\Application\Budget\ListBudgetsUseCase;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Budget\BudgetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/budgets?entityId=&fiscalTermId=&status= */
final readonly class ListBudgetController
{
    public function __construct(
        private ListBudgetsUseCase $useCase,
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

        $statusRaw = $request->queryString('status');
        $status = null;
        if ($statusRaw !== null) {
            $status = BudgetStatus::tryFrom($statusRaw);
            if ($status === null) {
                return ErrorResponse::badRequest('status must be one of draft|approved|locked.');
            }
        }

        $budgets = $this->useCase->execute($entityId, $fiscalTermId, $status);
        return EnvelopeResponse::list(
            BudgetJsonSerializer::toArrayList($budgets),
            ['total' => count($budgets)],
        );
    }
}
