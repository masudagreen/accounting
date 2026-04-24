<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\ListConsumptionTaxRatesUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;

/** GET /api/v1/consumption-tax/rates */
final readonly class ListConsumptionTaxRatesController
{
    public function __construct(
        private ListConsumptionTaxRatesUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $rates = $this->useCase->execute();
        return EnvelopeResponse::list(
            ConsumptionTaxSettlementJsonSerializer::ratesToArrayList($rates),
            ['total' => count($rates)],
        );
    }
}
