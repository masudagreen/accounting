<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\GenerateConsumptionTaxReportUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxReportGeneratorInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/consumption-tax/periods/{id}/report?format=json|pdf */
final readonly class GetConsumptionTaxReportController
{
    public function __construct(
        private GenerateConsumptionTaxReportUseCase $useCase,
        private ConsumptionTaxReportGeneratorInterface $generator,
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
        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->generator->render($settlement);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="consumption-tax-report.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(
            ConsumptionTaxSettlementJsonSerializer::settlementToArray($settlement),
        );
    }
}
