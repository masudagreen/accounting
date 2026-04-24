<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\CalculateConsumptionTaxUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/consumption-tax/periods/{id}/calculate */
final readonly class CalculateConsumptionTaxController
{
    public function __construct(
        private CalculateConsumptionTaxUseCase $useCase,
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
            $settlement = $this->useCase->execute($id);
        } catch (EntityNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        }
        return EnvelopeResponse::ok(
            ConsumptionTaxSettlementJsonSerializer::settlementToArray($settlement),
        );
    }
}
