<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\CashPlan;

use Rucaro\Application\CashPlan\GetCashPlanUseCase;
use Rucaro\Domain\CashPlan\CashPlanPdfGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\CashPlan\CashPlanJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/cash-plans/{id}?format=json|pdf */
final readonly class GetCashPlanController
{
    public function __construct(
        private GetCashPlanUseCase $useCase,
        private CashPlanPdfGeneratorInterface $generator,
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
        $plan = $this->useCase->execute($id);
        if ($plan === null) {
            return ErrorResponse::notFound(sprintf('cash plan %s not found.', $id));
        }
        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->generator->render($plan);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="cash-plan.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(CashPlanJsonSerializer::toArray($plan));
    }
}
