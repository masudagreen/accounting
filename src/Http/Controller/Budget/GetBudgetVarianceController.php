<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Budget;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceInput;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceUseCase;
use Rucaro\Application\Budget\GetBudgetUseCase;
use Rucaro\Domain\Budget\BudgetVariancePdfGeneratorInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Budget\BudgetVarianceJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * GET /api/v1/budgets/{id}/variance-analysis?asOf=YYYY-MM-DD&format=json|pdf
 *
 * Defaults:
 *   - `asOf` = today (UTC)
 *   - `from` = fiscal term start date looked up from the budget's
 *     fiscal_term_id. Callers can override via `?from=`.
 *   - `format` = json
 */
final readonly class GetBudgetVarianceController
{
    public function __construct(
        private GetBudgetUseCase $getBudget,
        private AnalyzeBudgetVarianceUseCase $analyze,
        private BudgetVariancePdfGeneratorInterface $generator,
        private AuthenticateBearer $auth,
        private PDO $pdo,
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

        $budget = $this->getBudget->execute($id);
        if ($budget === null) {
            return ErrorResponse::notFound(sprintf('budget %s not found.', $id));
        }

        $asOf = self::parseDate($request->queryString('asOf'))
            ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $from = self::parseDate($request->queryString('from'))
            ?? $this->lookupFiscalTermStart($budget->fiscalTermId)
            ?? $asOf;

        try {
            $analysis = $this->analyze->execute(new AnalyzeBudgetVarianceInput(
                budgetId: $id,
                fiscalTermStartDate: $from,
                asOf: $asOf,
            ));
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }

        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->generator->render($analysis);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="budget-variance.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(BudgetVarianceJsonSerializer::toArray($analysis));
    }

    private static function parseDate(?string $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private function lookupFiscalTermStart(string $fiscalTermId): ?DateTimeImmutable
    {
        $stmt = $this->pdo->prepare('SELECT start_date FROM fiscal_terms WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var string|false $raw */
        $raw = $stmt->fetchColumn();
        if ($raw === false || !is_string($raw) || $raw === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }
}
