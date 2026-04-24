<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\CashPlan;

use Rucaro\Application\CashPlan\DeleteCashPlanUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** DELETE /api/v1/cash-plans/{id} */
final readonly class DeleteCashPlanController
{
    public function __construct(
        private DeleteCashPlanUseCase $useCase,
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
        $this->useCase->execute($id);
        return EnvelopeResponse::ok(['id' => $id, 'deleted' => true]);
    }
}
