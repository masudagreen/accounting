<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\ListInvoiceRegistrationsUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/consumption-tax/invoice-registrations?entityId= */
final readonly class ListInvoiceRegistrationsController
{
    public function __construct(
        private ListInvoiceRegistrationsUseCase $useCase,
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
        $regs = $this->useCase->execute($entityId);
        return EnvelopeResponse::list(
            ConsumptionTaxSettlementJsonSerializer::invoiceRegistrationsToArrayList($regs),
            ['total' => count($regs)],
        );
    }
}
