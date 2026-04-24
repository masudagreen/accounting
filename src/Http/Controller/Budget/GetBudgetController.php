<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Budget;

use Rucaro\Application\Budget\GetBudgetUseCase;
use Rucaro\Domain\Budget\BudgetPdfGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Budget\BudgetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/budgets/{id}?format=json|pdf */
final readonly class GetBudgetController
{
    public function __construct(
        private GetBudgetUseCase $useCase,
        private BudgetPdfGeneratorInterface $generator,
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
        $budget = $this->useCase->execute($id);
        if ($budget === null) {
            return ErrorResponse::notFound(sprintf('budget %s not found.', $id));
        }
        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->generator->render($budget);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="budget.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(BudgetJsonSerializer::toArray($budget));
    }
}
